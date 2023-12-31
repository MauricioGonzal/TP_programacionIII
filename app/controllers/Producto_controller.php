<?php 
require_once './models/Producto.php';

class Producto_controller{

	public function cargarUno($request, $response, $args){
		$params = $request->getParsedBody();
		$producto = Producto::crearUno($params['descripcion'], $params['stock'], $params['precio'], $params['sector']);

	    if (Producto::insertarUno($producto) > 0) {
	      $payload = json_encode(array("mensaje" => "Producto creado con éxito"));
	    } else {
	      $payload = json_encode(array("mensaje" => "Error al crear el producto"));
	    }

		 $response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}

	public function modificarUno($request, $response, $args){
		$params = json_decode(file_get_contents('php://input'), true);
		$producto = Producto::getById($params['id']);
		if($producto!=false){
			$producto->precio = $params['precio'];
			$producto->cantidad = $params['cantidad'];
			$producto->descripcion = $params['descripcion'];

		    if (Producto::modificar($producto) != false) {
		      $payload = json_encode(array("mensaje" => "Producto modificado con éxito"));
		    } else {
		      $payload = json_encode(array("mensaje" => "Error al modificar el producto"));
		    }
		}
		else{
		    $payload = json_encode(array("mensaje" => "No existe producto con el id ingresado"));
		}


		 $response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}

	public function borrarUno($request, $response, $args){
		$params = json_decode(file_get_contents('php://input'), true);

	    if (Producto::borrar($params['id']) != false) {
	      $payload = json_encode(array("mensaje" => "Producto eliminado con éxito"));
	    } else {
	      $payload = json_encode(array("mensaje" => "Error al borrar el producto"));
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

	public function descargarDatosCsv($request, $response){
        $productos = Producto::getAll();
        $stream = fopen('php://temp', 'w+');
        foreach ($productos as $p) {
            fputcsv($stream, get_object_vars($p));
        }
        //fclose($stream);

        $response = $response->withHeader('Content-Type', 'text/csv');
        $response = $response->withHeader('Content-Disposition', 'attachment; filename="productos.csv"');
        $response = $response->withHeader('Pragma', 'no-cache');
        $response = $response->withHeader('Expires', '0');
        $response = $response->withBody(new \Slim\Psr7\Stream($stream));
        return $response;
	}

    public static function cargarDatosCsv($request, $response)
    {
        $CSV = $request->getUploadedFiles()['archivo'];
        $stream = $CSV->getStream();
        $content = ($stream)->getContents();
        $lines = explode("\n", $content);
        foreach ($lines as $l) {
            $data = str_getcsv($l);
            if (empty(trim($data[0]))) {
                break;
            }
            $producto = Producto::crearUno($data[1], $data[2], $data[3], $data[4]);
            if(Producto::insertarUno($producto)===false){
            	die('error');
            }
        }

        $payload = json_encode(array("mensaje" => "Archivo cargado con exito"));


        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }



}



?>