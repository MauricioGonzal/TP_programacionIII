<?php 
require_once './models/Pedido.php';
require_once './models/Encargo.php';

class Pedido_controller{

	public function cargarUno($request, $response, $args){
		$params = $request->getParsedBody();
		$pedido = Pedido::crearUno($params['producto'],$params['cantidad']);
		$id_pedido = Pedido::insertarUno($pedido);
		if ($id_pedido > 0) {
			$payload = json_encode(array("mensaje" => "Pedido creado exitosamente"));
	    } else if($id_pedido == -1){
	      $payload = json_encode(array("mensaje" => "No hay stock suficiente del producto"));
    	}
    	else{
	      $payload = json_encode(array("mensaje" => "Error al crear el pedido"));
    	}

	 	$response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}

	public function traerUno($request, $response, $args){
		$id = $args['id'];
		$pedido = Pedido::getById($id);

		if($pedido != null) $payload = json_encode($pedido);
		else $payload = json_encode(array('mensaje'=>'No existe el pedido'));

		$response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}

	public function traerTodos($request, $response, $args){
		$pedidos = Pedido::getAll();

		if(count($pedidos)>0){
			$payload = json_encode($pedidos);
		}
		else{
			$payload = json_encode(array('mensaje'=>'No hay pedidos cargados'));
		}

		$response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}

	public function modificarUno($request, $response, $args){
		$params = json_decode(file_get_contents('php://input'), true);
		$pedido = Producto::getById($params['id']);

		if($pedido!=false){
			$pedido->producto = $params['producto'];
			$pedido->cantidad = $params['cantidad'];

		    if (Pedido::modificar($pedido) != false) $payload = json_encode(array("mensaje" => "Pedido modificado con éxito"));
		    else $payload = json_encode(array("mensaje" => "Error al modificar el pedido"));
		}
		else $payload = json_encode(array("mensaje" => "No existe pedido con el id ingresado"));

		$response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}

}
?>