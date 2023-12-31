<?php
require_once './models/Usuario.php';
require_once './models/Log.php';
require_once './models/AutentificadorToken.php';
require_once './models/Sector.php';

class Usuario_controller{
	public function cargarUno($request, $response, $args){
		$params = $request->getParsedBody();
		$sector = Sector::getSector($params['sector'])->id;
		$usuario = Usuario::crearUno($params['nombre'], Usuario::getActivo(), $sector, $params['password'],$params['documento']);

	    if (Usuario::insertarUno($usuario) > 0) {
	      $payload = json_encode(array("mensaje" => "Usuario creado con exito."));
	    } else {
	      $payload = json_encode(array("mensaje" => "Error al crear el usuario."));
	    }
	 	$response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}

	public function crear_pdf_login($request, $response, $args){
      	$logs = Log::getAll();

      	$pdf = new FPDF();
		$pdf->AddPage();
		$pdf->SetFont('Arial', '', 12);
		$pdf->Cell(0, 0, 'INGRESOS AL SISTEMA :', 0, 0);
		$pdf->Ln(10);

		foreach($logs as $l){
			$usuario = Usuario::getById($l->usuario);
			$pdf->Cell(0, 0, $l->fecha . ' ' . $usuario->nombre, 0, 0);
			
			$pdf->Ln(10);

		}
		$pdf->Output('F', 'Logins.pdf');

      	$payload = json_encode(array("mensaje" => "Pdf creado correctamente"));

	 	$response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}

	public function crear_pdf_operaciones($request, $response, $args){
      	$logs_operaciones = Log_operacion::getAll();

      	$pdf = new FPDF();
		$pdf->AddPage();
		$pdf->SetFont('Arial', '', 12);
		$pdf->Cell(0, 0, 'Operaciones :', 0, 0);
		$pdf->Ln(10);

		foreach($logs_operaciones as $l){
			$usuario = Usuario::getById($l->usuario);
			switch ($l->accion) {
				case '/encargo/tomarEncargo':
					// code...
					$pdf->Cell(0, 0, $l->fecha . ' ' . 'Encargo tomado ID: '. $l->id_encargo . ' del pedido ID: '. $l->id_pedido .'.  '. $usuario->nombre , 0, 0);
					break;
				case '/pedido':
					$pdf->Cell(0, 0, $l->fecha . ' ' . 'Pedido creado. ID: '. $l->id_pedido . '.  ' .$usuario->nombre , 0, 0);
				break;
				case '/encargo/dejarParaServir':
					$pdf->Cell(0, 0, $l->fecha . ' ' . 'Encargo entregado para servir ID: '. $l->id_encargo . ' del pedido ID: '. $l->id_pedido . '.  '. $usuario->nombre , 0, 0);
				break;
				case '/pedido/servirPedido':
					$pdf->Cell(0, 0, $l->fecha . ' ' . 'Pedido servido. ID: '. $l->id_pedido . '.  ' . $usuario->nombre , 0, 0);					
				break;
				case '/pedido/cobrar':
					$pdf->Cell(0, 0, $l->fecha . ' ' . 'Pedido cobrado. ID: '. $l->id_pedido . '.  '. $usuario->nombre , 0, 0);					
				break;
				default:
					// code...
					break;
			}
			
			$pdf->Ln(10);

		}
		$pdf->Output('F', 'Operaciones.pdf');

      	$payload = json_encode(array("mensaje" => "Pdf creado correctamente"));

	 	$response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}

	public function modificarUno($request, $response, $args){
		$params = json_decode(file_get_contents('php://input'), true);
		$usuario = Usuario::getById($params['id']);
		if($usuario!=false){
			$usuario->nombre = $params['nombre'];
			$usuario->estado = $params['estado'];
			$usuario->sector = $params['sector'];
			$usuario->password = $params['password'];
			$usuario->documento = $params['documento'];

		    if (Usuario::modificar($usuario) != false) {
		      $payload = json_encode(array("mensaje" => "Usuario modificado con éxito"));
		    } else {
		      $payload = json_encode(array("mensaje" => "Error al modificar el usuario"));
		    }
		}
		else{
		    $payload = json_encode(array("mensaje" => "No existe usuario con el id ingresado"));
		}
		

		 $response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}

	public function borrarUno($request, $response, $args){
		$params = json_decode(file_get_contents('php://input'), true);

	    if (Usuario::borrar($params['id']) != false) {
	      $payload = json_encode(array("mensaje" => "Usuario eliminado con éxito"));
	    } else {
	      $payload = json_encode(array("mensaje" => "Error al borrar el usuario"));
	    }

		 $response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}

	public function traerUno($request, $response, $args){
		$id = $args['id'];
		$usuario = Usuario::getById($id);

		if($usuario != null){
			$payload = json_encode($usuario);
		}
		else{
			$payload = json_encode(array('mensaje'=>'No existe el usuario'));
		}

		$response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}

	public function traerTodos($request, $response, $args){
		$usuarios = Usuario::getAll();

		if(count($usuarios)>0){
			$payload = json_encode($usuarios);
		}
		else{
			$payload = json_encode(array('mensaje'=>'No hay usuarios cargados'));
		}

		$response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}

    public function login($request, $response){
        $parametros = $request->getParsedBody();
        $sector = Sector::getSector($parametros['sector']);

        if($sector != false){
        	$usuario = Usuario::verificarExistencia($parametros['user'], $parametros['documento'], $sector->id);
    		if($usuario != false && password_verify($parametros['password'], $usuario->password)){
            	$token = AutentificadorToken::crearToken(array('usuario'=>$usuario->id, 'password'=>$parametros['password'],'sector'=>$sector->id, 'documento'=>$usuario->documento));
            	$payload = json_encode(array('mensaje'=>'Inicio de sesion exitoso', 'token'=>json_encode($token)));
        		$log = Log::crearUno($usuario->id, date('d-m-Y h:i:s', time()));
        		Log::insertarUno($log);
        	}
        	else $payload = json_encode(array("No existe usuario con los datos ingresados"));
        } 
        else $payload = json_encode(array("No existe el sector ingresado"));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}



?>