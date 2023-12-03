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
        		$log = Log::crearUno($parametros['user'], date('d-m-Y h:i:s', time()));
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