<?php 
require_once './db/AccesoDatos.php';
class Producto
{
	
	function __construct($descripcion, $stock, $precio, $sector)
	{
		$this->descripcion = $descripcion;
		$this->stock = $stock;
		$this->precio = $precio;
		$this->sector = $sector;
	}

	public static function crearUno($descripcion, $stock, $precio, $sector){
		$producto = new Producto($descripcion, $stock, $precio, $sector);
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
        $query = $objDataAccess->prepararConsulta("INSERT INTO productos (descripcion, stock, precio, sector) 
        VALUES (:descripcion, :stock, :precio, :sector)");
        $query->bindValue(':descripcion', $producto->descripcion);
        $query->bindValue(':stock', $producto->stock);
        $query->bindValue(':precio', $producto->precio);
        $query->bindValue(':sector', $producto->sector);
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

	public static function modificar($producto){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("UPDATE productos SET descripcion=:descripcion, stock=:stock, precio=:precio, sector=:sector where id = :id");
        $query->bindValue(':descripcion', $producto->descripcion);
        $query->bindValue(':stock', $producto->stock);
        $query->bindValue(':precio', $producto->precio);
        $query->bindValue(':sector', $producto->sector);
        $query->bindValue(':id', $producto->id);
        $query->execute();

        return true;
	}

	public static function borrar($id){
		if(Producto::getById($id)!=false){
			$objDataAccess = AccesoDatos::obtenerInstancia();
	        $query = $objDataAccess->prepararConsulta("DELETE FROM productos where id = :id");
	        $query->bindValue(':id', $id);
	        $query->execute();

	        return true;
		}
		else{
			return false;
		}
		
	}

	public static function hayStock($producto, $cantidad){
		$producto = Producto::getById($producto);
		$stockRestante = $producto->stock - $cantidad;
		if($stockRestante >=0) return $stockRestante;
		else return false;
	}
}


?>