<?php 
require_once './models/Usuario.php';
require_once './models/Log.php';
class Usuario_controller{

	public function cargarUno($request, $response, $args){
		$params = $request->getParsedBody();
		$usuario = Usuario::crearUno($params['nombre'], $params['estado'], $params['sector'], $params['password']);

	    if (Usuario::insertarUno($usuario) > 0) {
	      $payload = json_encode(array("mensaje" => "usuario creado con éxito"));
	    } else {
	      $payload = json_encode(array("mensaje" => "Error al crear el usuario"));
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
		$encargosPendientes = Encargo::getBySector($params['sector']);

		if(count($encargosPendientes)>0){
			$payload = json_encode(array('mensaje'=>'Encargos Pendientes', 'encargos'=>$encargosPendientes));
		}
		else{
			$payload = json_encode(array('mensaje'=>'No hay encargos pendientes para el sector'));
		}

		$response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}



}



?>