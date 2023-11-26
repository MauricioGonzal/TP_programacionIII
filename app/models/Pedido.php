<?php 

require_once './models/Producto.php';

class Pedido{

	function __construct($mozo, $mesa, $numero)
	{
		$this->mozo = $mozo;
		$this->mesa = $mesa;
		$this->numero = $numero;
		$this->imagen = $imagen;
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
        $query = $objDataAccess->prepararConsulta("INSERT INTO pedidos (producto, cantidad, mesa) 
        VALUES (:producto, :cantidad, :mesa)");
        $query->bindValue(':producto', $pedido->producto);
        $query->bindValue(':cantidad', $pedido->cantidad);
        $query->bindValue(':mesa', $pedido->mesa);
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
        $query = $objDataAccess->prepararConsulta("UPDATE pedidos SET producto=:producto, cantidad=:cantidad, mesa=:mesa where id = :id");
        $query->bindValue(':producto', $pedido->producto);
        $query->bindValue(':cantidad', $pedido->cantidad);
        $query->bindValue(':mesa', $pedido->mesa);
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

}

//guardar clave de usuario con hash
//password_verify('1234', $hash)

//pedido puede tener mas de un producto, cada producto su propio estado(pedido), cada producto debiera tener su tiempo de preparacion


?>