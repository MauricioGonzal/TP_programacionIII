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
        $query = $objDataAccess->prepararConsulta("SELECT encargos.id as id_encargo,pedido, producto, cantidad, usuario,estado FROM encargos JOIN productos ON productos.id = encargos.producto where productos.sector = :sector AND estado = :estado");
        $query->bindValue(':sector', $sector);
        $query->bindValue(':estado', SELF::PENDIENTE);

        $query->execute();
         while ($fila = $query->fetchObject()){
         	array_push($encargos, $fila);
         }
        return $encargos;
	}

	public static function modificar($encargo){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("UPDATE encargos SET pedido=:pedido, producto=:producto, cantidad=:cantidad, usuario=:usuario, estado=:estado,tiempo_estipulado=:tiempo_estipulado where id = :id");
        $query->bindValue(':pedido', $encargo->pedido);
        $query->bindValue(':producto', $encargo->producto);
        $query->bindValue(':cantidad', $encargo->cantidad);
        $query->bindValue(':usuario', $encargo->usuario);
        $query->bindValue(':estado', $encargo->estado);
        $query->bindValue(':tiempo_estipulado', $encargo->tiempo_estipulado);
        $query->bindValue(':id', $encargo->id);
        $query->execute();

        return true;
	}

	public static function tomarEncargo($id, $usuario, $tiempo_preparacion){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("UPDATE encargos SET estado=1, usuario=:usuario, tiempo_preparacion = :tiempo_preparacion where id = :id");
        $query->bindValue(':usuario', $usuario);
        $query->bindValue(':tiempo_preparacion', $tiempo_preparacion);
        $query->bindValue(':id', $id);
        $query->execute();

        return true;

	}

	public static function sePuedeTomar($id_usuario, $id_encargo){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT * FROM encargos JOIN productos ON productos.id = encargos.producto JOIN usuarios ON usuarios.sector = productos.sector where encargos.id = :id_encargo AND usuarios.id = :id_usuario");
        $query->bindValue(':id_encargo', $id_encargo);
        $query->bindValue(':id_usuario', $id_usuario);
        $query->execute();

        return $query->fetchObject();

	}



}


?>