<?php
require_once './models/Encargo.php';
require_once './models/Mesa.php';
require_once './models/AutentificadorToken.php';

class Encargo_controller{

	public function tomar($request, $response, $args){
		$params = $request->getParsedBody();
		$dataUsuario = AutentificadorToken::obtenerData($request);
		if(Encargo::sePuedeTomar($dataUsuario->usuario, $params['id_encargo']) != false){
			Encargo::tomarEncargo($params['id_encargo'], $dataUsuario->usuario, $params['tiempo_preparacion']);
			$payload = json_encode(array("mensaje"=>'Encargo asignado correctamente'));
		}
		else{
			$payload = json_encode(array("mensaje"=>'El encargo ingresado no puede ser asignado. Verifique los datos.'));
		}

		$response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}

	public function dejarParaServir($request, $response, $args){
		$params = $request->getParsedBody();
		$dataUsuario = AutentificadorToken::obtenerData($request);
		if(Encargo::sePuedeServir($dataUsuario->usuario, $params['id_encargo']) != false){
			Encargo::dejarParaServir($params['id_encargo'], $dataUsuario->usuario, $params['tiempo_realpreparacion']);
			$payload = json_encode(array("mensaje"=>'Cambio de estado a listo para servir.'));
		}
		else{
			$payload = json_encode(array("mensaje"=>'El encargo no corresponde al usuario ingresado'));
		}

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

	public function listarPendientes($request, $response, $args){
		$dataUsuario = AutentificadorToken::obtenerData($request);
		$pendientes = Encargo::getPendientes($dataUsuario->sector);

		if(count($pendientes)>0){
			$payload = json_encode(array('Encargos Pendientes'=>$pendientes));
		}
		else{
			$payload = json_encode(array('msj'=>'No hay pedidos pendientes para el sector'));
		}


		$response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}

	public function listarEnPreparacion($request, $response, $args){
		$dataUsuario = AutentificadorToken::obtenerData($request);
		$enPreparacion = Encargo::getEnPreparacion($dataUsuario->usuario);

		if(count($enPreparacion)>0){
			$payload = json_encode(array('Encargos En preparacion'=>$enPreparacion));
		}
		else{
			$payload = json_encode(array('msj'=>'No hay pedidos en preparacion'));
		}

		$response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}
}
?>