<?php 
require_once './models/Usuario.php';
class Encargo{
	const PENDIENTE = 0;
	const ENPREPARACION = 1;
	const LISTOPARASERVIR = 2;


	function __construct($pedido, $producto, $cantidad, $usuario, $estado)
	{
		$this->pedido = $pedido;
		$this->producto = $producto;
		$this->cantidad = $cantidad;
		$this->usuario = $usuario;
		$this->estado = $estado;
	}

	public static function crearUno($pedido, $producto, $cantidad, $usuario, $estado){
		$encargo = new Encargo($pedido, $producto, $cantidad, $usuario, $estado);
		return $encargo;
	}

	public static function getById($id){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT * FROM encargos where id = :id");
        $query->bindValue(':id', $id);
        $query->execute();
        return $query->fetchObject();
	}

	public static function insertarUno($encargo){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("INSERT INTO encargos (pedido, producto, cantidad, usuario, estado) 
        VALUES (:pedido, :producto, :cantidad, :usuario, :estado)");
        $query->bindValue(':pedido', $encargo->pedido);
        $query->bindValue(':producto', $encargo->producto);
        $query->bindValue(':cantidad', $encargo->cantidad);
        $query->bindValue(':usuario', $encargo->usuario);
        $query->bindValue(':estado', $encargo->estado);
        $query->execute();

        return $objDataAccess->obtenerUltimoId();
	}

	public static function getAll(){
		$encargos = array();
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT * FROM encargos");
        $query->execute();
         while ($fila = $query->fetchObject()){
         	array_push($encargos, $fila);
         }
        return $encargos;
	}

	public static function getBySector($sector){
		$encargos = array();
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT * FROM encargos JOIN productos ON productos.id = encargos.producto where productos.sector = :sector AND estado = :estado");
        $query->bindValue(':sector', $sector);
        $query->bindValue(':estado', SELF::PENDIENTE);

        $query->execute();
         while ($fila = $query->fetchObject()){
         	array_push($encargos, $fila);
         }
        return $encargos;
	}



}


?>