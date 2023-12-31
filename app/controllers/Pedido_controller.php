<?php 
require_once './models/Pedido.php';
require_once './models/Encargo.php';
require_once './models/Mesa.php';
require_once './models/AutentificadorToken.php';
require_once './models/Log_operacion.php';

class Pedido_controller{

	public function cargarUno($request, $response, $args){
		$params = $request->getParsedBody();
		$msg = '';
		$payload =''; 
		if(Mesa::getByCodigo($params['mesa'])===false) $payload = json_encode(array("mensaje" => "No existe mesa con el codigo ingresado"));
		else{
	        $header = $request->getHeaderLine('Authorization');
            $token = trim(explode("Bearer", $header)[1]);
            AutentificadorToken::VerificarToken($token);
            $data = AutentificadorToken::obtenerPayload($token)->data;
			$pedido = Pedido::crearUno($data->usuario, $params['mesa'], $params['numero']);
			$id_pedido = Pedido::insertarUno($pedido);
			$productos = explode(',', $params['producto']);
			$cantidades = explode(',', $params['cantidad']);
			
			for ($i=0; $i < count($productos); $i++) { 
				// code...
				$id_producto = Producto::getIdByNombre($productos[$i]);
				$stockRestante =  Producto::hayStock($id_producto['id'], $cantidades[$i]);
				if($stockRestante!=false){
					$producto = Producto::getById($id_producto['id']);
					$producto->stock = $stockRestante;
					Producto::modificar($producto);
					$encargo = Encargo::crearUno($id_pedido,$id_producto['id'],$cantidades[$i], 0, 0);
					$id_encargo = Encargo::insertarUno($encargo);
					if($id_encargo === false){
  				      	$payload = json_encode(array("mensaje" => "Error al crear el pedido"));
				      	break;
			    	}	
				}
				else{
					$payload = json_encode(array("No hay stock del producto con ID: " . $id_producto['id'] . '. '));
					break;
				}
		
			}
			if($payload == ''){
				if(isset($_FILES['imagen']['tmp_name'])){
					Pedido::guardarImagen($id_pedido,$_FILES['imagen']['tmp_name'],$params['mesa']);
				}
				$payload = json_encode(array("msj"=>"Pedido creado exitosamente"));
				Mesa::cambiarEstado($params['mesa'], 2);
		        $recurso = $request->getUri();	
		    	$recurso = substr((string)$recurso, 20);
				$log_operacion = Log_operacion::crearUno($data->usuario, $recurso, $id_pedido, 0);
				Log_operacion::insertarUno($log_operacion);
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

	public function subirImagen($request, $response){
		$params = $request->getParsedBody();
		$pedido = Pedido::getById($params['id_pedido']);
		if($pedido!=false){
			if(Pedido::guardarImagen($params['id_pedido'],$_FILES['imagen']['tmp_name'], $pedido->mesa)){
		      $payload = json_encode(array("mensaje" => "Imagen subida con exito"));
			}
			else{
		      $payload = json_encode(array("mensaje" => "Error al subir la imagen"));
			}
		}
		else{
	      $payload = json_encode(array("mensaje" => "No existe pedido con el id ingresado"));

		}


	 	$response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}

	public function obtenerTiempoDemora($request, $response){
		$params = $request->getParsedBody();

		$pedido = Pedido::getByPedidoYMesa($params['numero_pedido'], $params['codigo_mesa']);

		if($pedido != false){
			if(count(Encargo::getPendientesByNumeroPedido($params['numero_pedido'])) === 0){
				$resultado = Pedido::getTiempoDemora($pedido)->tiempo_demora;
				if($resultado != 0){
					$payload = json_encode(array("mensaje" => "El tiempo de demora de su pedido es de " . $resultado . " minutos"));
				}
				else{
					$payload = json_encode(array("mensaje" => "El pedido aun esta a la espera de ser asignado."));
				}
			}
			else{
				$payload = json_encode(array("mensaje" => "El pedido aun no ha sido asignado en su totalidad. Intente en unos minutos."));	
			}
			
			
		}
		else{
			$payload = json_encode(array("mensaje" => "No existe un pedido registrado con los datos ingresados."));
		}



	 	$response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');

	}

	public function listarPedidos($request, $response, $args){
		$pedidos = Pedido::getAll();
		$msj = array();
		foreach($pedidos as $p){
			if(count(Encargo::getPendientesByNumeroPedido($p->numero))===0){
				$resultado = Pedido::getTiempoDemora($p)->tiempo_demora;
				$item['id_pedido'] = $p->id;
				$item['tiempo_demora'] = $resultado . ' minutos';
				array_push($msj, $item);
			}
			else{
				$item['id_pedido'] = $p->id;
				$item['tiempo_demora'] = 'El pedido aun no ha sigo asignado en su totalidad.';
				array_push($msj, $item);
			}


		}

		if(count($msj)===0) $payload = json_encode(array("mensaje" => 'No hay pedidos asignados en tu totalidad.'));
		else $payload = json_encode(array("Pedidos" => $msj));

	 	$response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}

	public function cobrar($request, $response, $args){
		$params = $request->getParsedBody();
		Mesa::cambiarEstado($params['mesa'], 4);
		$importe = Encargo::getImporteTotalByPedido($params['id_pedido']);
		$payload = json_encode(array("mensaje"=>'Cobrando mesa. Importe total: ' . $importe));
        $recurso = $request->getUri();
    	$recurso = substr((string)$recurso, 20);
        $data = AutentificadorToken::obtenerData($request);
        $recurso = $request->getUri();	
    	$recurso = substr((string)$recurso, 20);
		$log_operacion = Log_operacion::crearUno($data->usuario, $recurso, $params['id_pedido'], 0);
		Log_operacion::insertarUno($log_operacion);

		$response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');

	}

	public function listarListosParaServir($request, $response){
		$params = $request->getParsedBody();

		$pedidosListosParaServir = Pedido::listosParaServir();

		if(count($pedidosListosParaServir)>0){
			$payload = json_encode(array('mensaje'=>'Pedidos listos para servir', 'Pedidos'=>$pedidosListosParaServir));
		}
		else{
			$payload = json_encode(array('mensaje'=>'No hay pedidos para servir'));
		}

		$response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');
	}

	public function servir($request, $response, $args){
		$params = $request->getParsedBody();
		$pedido = Pedido::getByPedidoYMesa($params['pedido'], $params['codigo_mesa']);
		if($pedido === false){
			$payload = json_encode(array("mensaje"=>'No existe pedido con el numero ingresado'));
		}
		else if(count(Encargo::isPedidoListoParaServir($pedido->id))>0){
			$payload = json_encode(array("mensaje"=>'El pedido aun no puede ser servido. Quedan encargos pendientes.'));
		}
		else{
            $data = AutentificadorToken::obtenerData($request);
			Mesa::cambiarEstado($params['codigo_mesa'], 3);
			$payload = json_encode(array("mensaje"=>'Pedido servido correctamente'));
	        $recurso = $request->getUri();	
	    	$recurso = substr((string)$recurso, 20);
			$log_operacion = Log_operacion::crearUno($data->usuario, $recurso, $params['pedido'], 0);
			Log_operacion::insertarUno($log_operacion);
		}

		$response->getBody()->write($payload);
    	return $response
      	->withHeader('Content-Type', 'application/json');

	}

}
?>