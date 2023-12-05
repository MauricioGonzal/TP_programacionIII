<?php 
require_once './models/Mesa.php';

class Mesa_controller{

	public function cargarUna($request, $response, $args){
		$params = $request->getParsedBody();
		if(Mesa::getByCodigo($params['codigo']) === false){
			$mesa = Mesa::crearUno($params['codigo'], Mesa::getCerrada());
		    if (Mesa::insertarUno($mesa) > 0) $payload = json_encode(array("mensaje" => "Mesa creada con éxito"));
	 		else $payload = json_encode(array("mensaje" => "Error al crear la mesa"));
		}
		else $payload = json_encode(array("mensaje" => "Ya existe una mesa con el codigo ingresado"));

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

	public function modificarUno($request, $response, $args){
		$params = json_decode(file_get_contents('php://input'), true);
		$mesa = Mesa::getById($params['id']);
		if($mesa!=false){
			$mesa->codigo = $params['codigo'];
			$mesa->estado = $params['estado'];

		    if (Mesa::modificar($mesa) != false) {
		      $payload = json_encode(array("mensaje" => "Mesa modificada con éxito"));
		    } else {
		      $payload = json_encode(array("mensaje" => "Error al modificar la mesa"));
		    }
		}
		else{
		      $payload = json_encode(array("mensaje" => "No existe mesa con el id ingresado"));
		}

		 $response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}

	public function borrarUno($request, $response, $args){
		$params = json_decode(file_get_contents('php://input'), true);

	    if (Mesa::borrar($params['id']) != false) {
	      $payload = json_encode(array("mensaje" => "Mesa eliminada con éxito"));
	    } else {
	      $payload = json_encode(array("mensaje" => "Error al borrar la mesa"));
	    }

		 $response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}

	public function cerrar($request, $response, $args){
		$params = $request->getParsedBody();
		if(Mesa::getByCodigo($params['mesa'])!== false){
			Mesa::cambiarEstado($params['mesa'], 1);
			$payload = json_encode(array("mensaje"=>'Mesa cerrada correctamente'));
		}
		else{
			$payload = json_encode(array("mensaje"=>'La mesa ingresada no existe.'));
		}


		$response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}

	public function getMasUsada($request, $response, $args){
		$params = $request->getParsedBody();
		$mesaMasUsada = Mesa::getMasUsada();
		$payload = json_encode(array("mensaje"=>'La mesa mas usada fue la mesa con el codigo: ' . $mesaMasUsada->mesa . '. Se ha usado ' . $mesaMasUsada->total . ' veces'));

		$response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}



}



?>


?>