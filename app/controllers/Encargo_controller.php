<?php 

require_once './models/Encargo.php';
require_once './models/Mesa.php';

class Encargo_controller{

	public function tomar($request, $response, $args){
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

	public function dejarParaServir($request, $response, $args){
		$params = $request->getParsedBody();
		if(Encargo::sePuedeServir($params['usuario'], $params['id_encargo']) != false){
			Encargo::dejarParaServir($params['id_encargo'], $params['usuario'], $params['tiempo_realpreparacion']);
			$payload = json_encode(array("mensaje"=>'Cambio de esado a listo para servir.'));
		}
		else{
			$payload = json_encode(array("mensaje"=>'El encargo no corresponde al usuario ingresado'));
		}

		$response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}

	public function servir($request, $response, $args){
		$params = $request->getParsedBody();
		Mesa::cambiarEstado($params['mesa'], 2);
		$payload = json_encode(array("mensaje"=>'Encargo servido correctamente'));

		$response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');

	}

	public function listarListosParaServir($request, $response){
		$params = $request->getParsedBody();

		$encargosListosParaServir = Encargo::getListosParaServir();

		if(count($encargosListosParaServir)>0){
			$payload = json_encode(array('mensaje'=>'Encargos listos para servir', 'encargos'=>$encargosListosParaServir));
		}
		else{
			$payload = json_encode(array('mensaje'=>'No hay encargos para servir'));
		}

		$response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}
}
?>