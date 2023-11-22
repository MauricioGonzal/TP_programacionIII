<?php 

require_once './models/Producto.php';

class Pedido{

	function __construct($producto, $cantidad)
	{
		$this->producto = $producto;
		$this->cantidad = $cantidad;
	}

	public static function crearUno($producto, $cantidad){
		$pedido = new Pedido($producto, $cantidad);
		return $pedido;
	}

	public static function getById($id){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT * FROM pedidos where id = :id");
        $query->bindValue(':id', $id);
        $query->execute();
        return $query->fetchObject();
	}

	public static function insertarUno($pedido){

		$stockRestante =  Producto::hayStock($pedido->producto, $pedido->cantidad);
		if($stockRestante!==false){
			$producto = Producto::getById($pedido->producto);
			$producto->stock = $stockRestante;
			Producto::modificar($producto);

			$objDataAccess = AccesoDatos::obtenerInstancia();
	        $query = $objDataAccess->prepararConsulta("INSERT INTO pedidos (producto, cantidad) 
	        VALUES (:producto, :cantidad)");
	        $query->bindValue(':producto', $pedido->producto);
	        $query->bindValue(':cantidad', $pedido->cantidad);
	        $query->execute();
	        return $objDataAccess->obtenerUltimoId();
		}
		else return -1;
        
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
        $query = $objDataAccess->prepararConsulta("UPDATE pedidos SET producto=:producto, cantidad=:cantidad where id = :id");
        $query->bindValue(':producto', $pedido->producto);
        $query->bindValue(':cantidad', $pedido->cantidad);
        $query->bindValue(':id', $pedido->id);
        $query->execute();

        return true;
	}

}

//guardar clave de usuario con hash
//password_verify('1234', $hash)

//pedido puede tener mas de un producto, cada producto su propio estado(pedido), cada producto debiera tener su tiempo de preparacion


?>