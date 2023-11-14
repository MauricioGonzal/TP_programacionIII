<?php 
require_once './models/Pedido.php';
require_once './models/Encargo.php';

class Pedido_controller{

	public function cargarUno($request, $response, $args){
		$params = $request->getParsedBody();
		$pedido = Pedido::crearUno($params['mesa'],$params['numero'], 0, $params['usuario']);
		$id_pedido = Pedido::insertarUno($pedido);
		if ($id_pedido > 0) {
			$encargos = json_decode($params['encargos']);
			foreach($encargos as $producto => $cantidad){
				$encargo = Encargo::crearUno($id_pedido, $producto, $cantidad, 0, 0);
				if(!(Encargo::insertarUno($encargo) > 0)){
			      $payload = json_encode(array("mensaje" => "Error al crear el pedido"));
	      		$response->getBody()->write($payload);
				return $response
      			->withHeader('Content-Type', 'application/json');
				}
			}
			$payload = json_encode(array("mensaje" => "Pedido creado exitosamente"));
	    } else {
	      $payload = json_encode(array("mensaje" => "Error al crear el pedido"));
    	}

		 $response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}

	public function traerUno($request, $response, $args){
		$id = $args['id'];
		$producto = Producto::getById($id);

		if($producto != null){
			$payload = json_encode($producto);
		}
		else{
			$payload = json_encode(array('mensaje'=>'No existe el producto'));
		}

		$response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}

	public function traerTodos($request, $response, $args){
		$productos = Producto::getAll();

		if(count($productos)>0){
			$payload = json_encode($productos);
		}
		else{
			$payload = json_encode(array('mensaje'=>'No hay productos cargados'));
		}

		$response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}



}



?>