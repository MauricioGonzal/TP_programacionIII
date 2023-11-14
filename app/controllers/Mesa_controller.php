<?php 
require_once './models/Mesa.php';
class Mesa_controller{

	public function cargarUna($request, $response, $args){
		$params = $request->getParsedBody();
		$mesa = Mesa::crearUno($params['codigo'], $params['estado']);

	    if (Mesa::insertarUno($mesa) > 0) {
	      $payload = json_encode(array("mensaje" => "Mesa creada con éxito"));
	    } else {
	      $payload = json_encode(array("mensaje" => "Error al crear la mesa"));
	    }

		 $response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}

	public function traerUno($request, $response, $args){
		$id = $args['id'];
		$mesa = Mesa::getById($id);

		if($mesa != null){
			$payload = json_encode($mesa);
		}
		else{
			$payload = json_encode(array('mensaje'=>'No existe la mesa'));
		}

		$response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}

	public function traerTodos($request, $response, $args){
		$mesas = Mesa::getAll();

		if(count($mesas)>0){
			$payload = json_encode($mesas);
		}
		else{
			$payload = json_encode(array('mensaje'=>'No hay mesas cargadas'));
		}

		$response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}



}



?>


?>