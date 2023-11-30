<?php 

require_once './models/Encargo.php';

class Encargo_controller{

	public function tomarEncargo($request, $response, $args){
		$params = $request->getParsedBody();
		if(Encargo::sePuedeTomar($params['usuario'], $params['id_encargo']) != false){
			Encargo::tomarEncargo($params['id_encargo'], $params['usuario'], $params['tiempo_preparacion']);
			$payload = json_encode(array("mensaje"=>'Encargo asignado correctamente'));
		}
		else{
			$payload = json_encode(array("mensaje"=>'El encargo no corresponde al usuario ingresado'));
		}

		$response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}	





}



?>