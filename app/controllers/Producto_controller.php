<?php 
require_once './models/Producto.php';
class Producto_controller{

	public function cargarUno($request, $response, $args){
		$params = $request->getParsedBody();
		$producto = Producto::crearUno($params['descripcion'], $params['stock'], $params['precio']);

	    if (Producto::insertarUno($producto) > 0) {
	      $payload = json_encode(array("mensaje" => "Producto creado con éxito"));
	    } else {
	      $payload = json_encode(array("mensaje" => "Error al crear el producto"));
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