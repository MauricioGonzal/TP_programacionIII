<?php 

require_once './models/Encargo.php';

class Encargo_controller{

	public function tomarEncargo($request, $response, $args){
		$params = $request->getParsedBody();
		Encargo::tomarEncargo($params['id_encargo'], $params['usuario'], $params['tiempo_preparacion']);
		$payload = json_encode(array("mensaje"=>'Encargo asignado correctamente'));

		$response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}	





}



?>