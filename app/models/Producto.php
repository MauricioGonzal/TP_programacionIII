<?php 
require_once './db/AccesoDatos.php';
class Producto
{
	
	function __construct($descripcion, $stock, $precio)
	{
		$this->descripcion = $descripcion;
		$this->stock = $stock;
		$this->precio = $precio;
	}

	public static function crearUno($descripcion, $stock, $precio){
		$producto = new Producto($descripcion, $stock, $precio);
		return $producto;
	}

	public static function getById($id){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT * FROM productos where id = :id");
        $query->bindValue(':id', $id);
        $query->execute();
        return $query->fetchObject();
	}

	public static function insertarUno($producto){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("INSERT INTO productos (descripcion, stock, precio) 
        VALUES (:descripcion, :stock, :precio)");
        $query->bindValue(':descripcion', $producto->descripcion);
        $query->bindValue(':stock', $producto->stock);
        $query->bindValue(':precio', $producto->precio);
        $query->execute();

        return $objDataAccess->obtenerUltimoId();
	}

	public static function getAll(){
		$productos = array();
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT * FROM productos");
        $query->execute();
         while ($fila = $query->fetchObject()){
         	array_push($productos, $fila);
         }
        return $productos;
	}
}


?>