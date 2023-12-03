<?php 

require_once './models/Producto.php';

class Pedido{

	function __construct($mozo, $mesa, $numero)
	{
		$this->mozo = $mozo;
		$this->mesa = $mesa;
		$this->numero = $numero;
	}

	public static function crearUno($mozo, $mesa, $numero){
		$pedido = new Pedido($mozo, $mesa, $numero);
		return $pedido;
	}

	public static function getById($id){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT * FROM pedidos where id = :id");
        $query->bindValue(':id', $id);
        $query->execute();
        return $query->fetchObject();
	}

	public static function getByPedidoYMesa($numero_pedido, $codigo_mesa){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT * FROM pedidos where mesa = :mesa AND numero = :numero");
        $query->bindValue(':mesa', $codigo_mesa);
        $query->bindValue(':numero', $numero_pedido);
        $query->execute();
        return $query->fetchObject();
	}

	public function getByNumero($numero){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT * FROM pedidos where numero = :numero");
        $query->bindValue(':numero', $numero);
        $query->execute();
        return $query->fetchObject();
	}

	/*public static function insertarUno($pedido){

		$stockRestante =  Producto::hayStock($pedido->producto, $pedido->cantidad, $pedido->mesa);
		if($stockRestante!==false){
			$producto = Producto::getById($pedido->producto);
			$producto->stock = $stockRestante;
			Producto::modificar($producto);

			$objDataAccess = AccesoDatos::obtenerInstancia();
	        $query = $objDataAccess->prepararConsulta("INSERT INTO pedidos (producto, cantidad, mesa) 
	        VALUES (:producto, :cantidad, :mesa)");
	        $query->bindValue(':producto', $pedido->producto);
	        $query->bindValue(':cantidad', $pedido->cantidad);
	        $query->bindValue(':mesa', $pedido->mesa);
	        $query->execute();
	        return $objDataAccess->obtenerUltimoId();
		}
		else return -1;
        
	}*/

	public static function insertarUno($pedido){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("INSERT INTO pedidos (mesa, mozo, numero) 
        VALUES (:mesa, :mozo, :numero)");
        $query->bindValue(':mozo', $pedido->mozo);
        $query->bindValue(':mesa', $pedido->mesa);
        $query->bindValue(':numero', $pedido->numero);
        $query->execute();
        return $objDataAccess->obtenerUltimoId();
	}

	public static function getAll(){
		$pedidos = array();
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT * FROM pedidos");
        $query->execute();
         while ($fila = $query->fetchObject()){
         	array_push($pedidos, $fila);
         }
        return $pedidos;
	}

	public static function modificar($pedido){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("UPDATE pedidos SET mesa=:mesa, mozo=:mozo, numero=:numero where id = :id");
        $query->bindValue(':mesa', $pedido->mesa);
        $query->bindValue(':mozo', $pedido->mozo);
        $query->bindValue(':numero', $pedido->numero);
        $query->bindValue(':id', $pedido->id);
        $query->execute();

        return true;
	}

	public static function borrar($id){
		if(Pedido::getById($id)!=false){
			$objDataAccess = AccesoDatos::obtenerInstancia();
	        $query = $objDataAccess->prepararConsulta("DELETE FROM mesa where id = :id");
	        $query->bindValue(':id', $id);
	        $query->execute();
	        return true;
		}
		else{
			return false;
		}
		
	}

	public static function guardarImagen($id_pedido, $filetmp, $id_mesa){
			$filename = 'pedido' . $id_pedido . 'mesa' . $id_mesa . '.jpg';
			if(move_uploaded_file($filetmp, 'imagenes_pedidos' .'/' . $filename)){
				return true;
			}
			return false;
	}

	public static function getTiempoDemora($pedido){
		$pedidos = array();
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT max(encargos.tiempo_preparacion) as tiempo_demora FROM encargos where encargos.pedido = :pedido");
        $query->bindValue(':pedido', $pedido->id);
        $query->execute();
        
        return $query->fetchObject();
	}

	public static function listosParaServir(){
		$pedidos = Pedido::getAll();
		$listos = array();
		if($pedidos != false){
			foreach($pedidos as $p){
				if(count(Encargo::isPedidoListoParaServir($p->id)) === 0) array_push($listos, $p);
			}

		}
        return $listos;
	}

}

//pedido puede tener mas de un producto, cada producto su propio estado(pedido), cada producto debiera tener su tiempo de preparacion


?>