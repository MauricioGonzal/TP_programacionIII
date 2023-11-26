<?php 
require_once './models/Pedido.php';
require_once './models/Encargo.php';
require_once './models/Mesa.php';

class Pedido_controller{

	public function cargarUno($request, $response, $args){
		//$_FILES['imagen']['tmp_name'];
		$params = $request->getParsedBody();
		$msg = '';
		if(Mesa::getByCodigo($params['mesa'])===false) $payload = json_encode(array("mensaje" => "No existe mesa con el codigo ingresado"));
		else{
			$pedido = Pedido::crearUno($params['mozo'], $params['mesa'], $params['numero']);
			$id_pedido = Pedido::insertarUno($pedido);
			$productos = explode(',', $params['producto']);
			$cantidades = explode(',', $params['cantidad']);
			
			for ($i=0; $i < count($productos); $i++) { 
				// code...
				$stockRestante =  Producto::hayStock($productos[$i], $cantidades[$i]);
				if($stockRestante!=false){
					$producto = Producto::getById($productos[$i]);
					$producto->stock = $stockRestante;
					Producto::modificar($producto);

					$encargo = Encargo::crearUno($id_pedido,$productos[$i],$cantidad[$i], 0, 0);
					$id_encargo = Encargo::insertarUno($encargo);
					if($id_encargo === false){
  				      	$payload = json_encode(array("mensaje" => "Error al crear el pedido"));
				      	break;
			    	}	
				}
				else{
					$msg .= "No hay stock del producto con ID: " . $productos[$i] . '. '; 
				}
				
				
		
			}

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

	public function borrarUno($request, $response, $args){
		$params = json_decode(file_get_contents('php://input'), true);

	    if (Pedido::borrar($params['id']) != false) {
	      $payload = json_encode(array("mensaje" => "Pedido eliminada con éxito"));
	    } else {
	      $payload = json_encode(array("mensaje" => "Error al borrar el pedido"));
	    }

		 $response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}

}
?>