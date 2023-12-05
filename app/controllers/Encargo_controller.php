<?php
require_once './models/Encargo.php';
require_once './models/Mesa.php';
require_once './models/AutentificadorToken.php';
require_once './models/Log_operacion.php';

class Encargo_controller{

	public function traerUno($request, $response, $args){
		$id = $args['id'];
		$pedido = Encargo::getById($id);

		if($pedido != null) $payload = json_encode($pedido);
		else $payload = json_encode(array('mensaje'=>'No existe el encargo'));

		$response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}

	public function traerTodos($request, $response, $args){
		$encargos = Encargo::getAll();

		if(count($encargos)>0){
			$payload = json_encode(array('ENCARGOS' => $encargos));
		}
		else{
			$payload = json_encode(array('mensaje'=>'No hay encargos cargados'));
		}

		$response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}

	public function tomar($request, $response, $args){
		$params = $request->getParsedBody();
		$dataUsuario = AutentificadorToken::obtenerData($request);
		if(Encargo::sePuedeTomar($dataUsuario->usuario, $params['id_encargo']) != false){
			Encargo::tomarEncargo($params['id_encargo'], $dataUsuario->usuario, $params['tiempo_preparacion']);
			$payload = json_encode(array("mensaje"=>'Encargo asignado correctamente'));
	        $recurso = $request->getUri();
        	$recurso = substr((string)$recurso, 20);
        	$encargo = Encargo::getById($params['id_encargo']);
			$log_operacion = Log_operacion::crearUno($dataUsuario->usuario, $recurso, $encargo->pedido, $params['id_encargo']);
			Log_operacion::insertarUno($log_operacion);
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
	        $recurso = $request->getUri();
        	$recurso = substr((string)$recurso, 20);
        	$encargo = Encargo::getById($params['id_encargo']);
			$log_operacion = Log_operacion::crearUno($dataUsuario->usuario, $recurso, $encargo->pedido, $params['id_encargo']);
			Log_operacion::insertarUno($log_operacion);
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

	public function encargosFueraDeTiempo($request, $response, $args){
		$dataUsuario = AutentificadorToken::obtenerData($request);

		$encargos = Encargo::getFueraDeTiempo();

		if(count($encargos)>0) {
			$payload = json_encode(array('Encargos entregados fuera de tiempo'=>$encargos));
		}
		else{
			$payload = json_encode(array('msj'=>'No hay encargos entregados fuera de tiempo'));
		}
		$response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}
}
?>