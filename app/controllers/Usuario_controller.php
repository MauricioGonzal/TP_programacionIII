<?php 
require_once './models/Usuario.php';
require_once './models/Log.php';
require_once './models/AutentificadorToken.php';
require_once './models/Sector.php';

class Usuario_controller{

	public function cargarUno($request, $response, $args){
		$params = $request->getParsedBody();
		$sector = Sector::getSector($params['sector'])->id;
		$usuario = Usuario::crearUno($params['nombre'], $params['estado'], $sector, $params['password'],$params['documento']);

	    if (Usuario::insertarUno($usuario) > 0) {
	      $payload = json_encode(array("mensaje" => "Usuario creado con exito."));
	    } else {
	      $payload = json_encode(array("mensaje" => "Error al crear el usuario."));
	    }
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

	public function listarPedidos($request, $response){
		$params = $request->getParsedBody();
		$empleado = Usuario::getById($params['empleado']);

		if($empleado !== false){
			$encargosPendientes = Encargo::getBySector($empleado->sector);
		}
		else{
			$payload = json_encode(array('mensaje'=>'No existe el empleado ingresado'));
			$response->getBody()->write($payload);
    		return $response
     	 	->withHeader('Content-Type', 'application/json');

		}
		

		if(count($encargosPendientes)>0){
			$payload = json_encode(array('mensaje'=>'Encargos Pendientes', 'encargos'=>$encargosPendientes));
		}
		else{
			$payload = json_encode(array('mensaje'=>'No hay encargos pendientes para el empleado ingresado'));
		}

		$response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}

    public function login($request, $response){
        $parametros = $request->getParsedBody();
        $usuario = Usuario::getByUser($parametros['user'], $parametros['documento'], $parametros['sector']);
        if($usuario != false && password_verify($parametros['password'], $usuario->password)){
            $token = AutentificadorToken::crearToken(array('usuario'=>$parametros['user'], 'password'=>$parametros['password'],'sector'=>$parametros['sector'], 'documento'=>$parametros['documento']));
            $payload =  json_encode(array('mensaje'=>'Inicio de sesion exitoso', 'token'=>json_encode($token)));
        }
        else{
            $payload = json_encode(array("No existe usuario con los datos ingresados"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }



}



?>