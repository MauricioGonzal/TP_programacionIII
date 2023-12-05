<?php 
require_once './models/Encuesta.php';
require_once './models/Pedido.php';
class Encuesta_controller{

	public function cargarUna($request, $response, $args){
		$params = $request->getParsedBody();
		$pedido = Pedido::getById($params['id_pedido']);
		if($pedido != false){
			$encuesta = Encuesta::crearUno($params['id_pedido'], $params['puntuacion_mesa'], $params['puntuacion_restaurante'], $params['puntuacion_mozo'], $params['puntuacion_cocinero'], $params['texto']);

		    if (Encuesta::insertarUno($encuesta) > 0) {
		      $payload = json_encode(array("mensaje" => "Encuesta creada con éxito"));
		    } else {
		      $payload = json_encode(array("mensaje" => "Error al crear la encuesta"));
		    }
		}
		else{
	      $payload = json_encode(array("mensaje" => "No existe el pedido ingresado. Verifique los datos."));	
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

	public function listarMejoresComentarios($request, $response, $args){
		$encuestas = Encuesta::getMejores();

		if(count($encuestas)>0){
	      $payload = json_encode(array("Mejores encuestas" => $encuestas));
		}
		else{
	      $payload = json_encode(array("mensaje" => "No hay encuestas cargadas"));
		}

	 	$response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}



}



?>


?>