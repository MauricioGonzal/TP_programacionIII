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

	public static function getBySector($sector, $estado){
		$encargos = array();
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT encargos.id as id_encargo,pedido, producto, cantidad, usuario,estado FROM encargos JOIN productos ON productos.id = encargos.producto where productos.sector = :sector AND estado = :estado");
        $query->bindValue(':sector', $sector);
        $query->bindValue(':estado', $estado);

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
        $query = $objDataAccess->prepararConsulta("SELECT * FROM encargos JOIN productos ON productos.id = encargos.producto JOIN usuarios ON usuarios.sector = productos.sector where encargos.id = :id_encargo AND usuarios.id = :id_usuario AND encargos.estado = :estado");
        $query->bindValue(':id_encargo', $id_encargo);
        $query->bindValue(':id_usuario', $id_usuario);
        $query->bindValue(':estado', SELF::PENDIENTE);
        $query->execute();

        return $query->fetchObject();

	}

	public static function sePuedeServir($id_usuario, $id_encargo){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT * FROM encargos JOIN productos ON productos.id = encargos.producto JOIN usuarios ON usuarios.sector = productos.sector where encargos.id = :id_encargo AND usuarios.id = :id_usuario AND encargos.estado = :estado");
        $query->bindValue(':id_encargo', $id_encargo);
        $query->bindValue(':id_usuario', $id_usuario);
        $query->bindValue(':estado', SELF::ENPREPARACION);
        $query->execute();

        return $query->fetchObject();

	}

	public static function dejarParaServir($id, $usuario, $tiempo_realpreparacion){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("UPDATE encargos SET estado=2, tiempo_realpreparacion = :tiempo_realpreparacion where id = :id");
        $query->bindValue(':tiempo_realpreparacion', $tiempo_realpreparacion);
        $query->bindValue(':id', $id);
        $query->execute();

        return true;

	}

	public static function getListosParaServir(){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT * FROM encargos where estado = :estado");
        $query->bindValue(':estado', SELF::LISTOPARASERVIR);
        $query->execute();
        $encargos = array();

     	while ($fila = $query->fetchObject()){
         	array_push($encargos, $fila);
     	}

        return $encargos;
	}

	public static function isPedidoListoParaServir($pedido){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT * FROM encargos where (estado = :estado  OR estado=:pendiente) AND pedido = :pedido");
        $query->bindValue(':estado', SELF::ENPREPARACION);
        $query->bindValue(':pedido', $pedido);
        $query->bindValue(':pendiente', SELF::PENDIENTE);
        $query->execute();
        $encargos = array();

     	while ($fila = $query->fetchObject()){
         	array_push($encargos, $fila);
     	}

        return $encargos;
	}

	public static function getPendientes($sector){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT encargos.id, productos.descripcion, encargos.cantidad FROM encargos JOIN productos ON productos.id = encargos.producto where estado = :estado AND sector = :sector");
        $query->bindValue(':estado', SELF::PENDIENTE);
        $query->bindValue(':sector', $sector);
        $query->execute();
        $encargos = array();

     	while ($fila = $query->fetchObject()){
         	array_push($encargos, $fila);
     	}

        return $encargos;
	}

	public static function getEnPreparacion($id_usuario){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT encargos.id, productos.descripcion, usuarios.nombre, encargos.estado FROM encargos JOIN productos ON productos.id= encargos.producto JOIN usuarios ON usuarios.id = encargos.usuario where encargos.usuario = :usuario AND encargos.estado = :estado");
        $query->bindValue(':estado', SELF::ENPREPARACION);
        $query->bindValue(':usuario', $id_usuario);
        $query->execute();
        $encargos = array();

     	while ($fila = $query->fetchObject()){
            $fila->estado = 'EN PREPARACION';
         	array_push($encargos, $fila);
     	}

        return $encargos;
	}

	public static function getPendientesByNumeroPedido($pedido){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT * from pedidos JOIN encargos ON pedidos.id = encargos.pedido where pedidos.numero = :pedido AND encargos.estado = :estado");
        $query->bindValue(':pedido', $pedido);
        $query->bindValue(':estado', SELF::PENDIENTE);
        $query->execute();
        $encargos = array();

     	while ($fila = $query->fetchObject()){
         	array_push($encargos, $fila);
     	}

        return $encargos;
	}

    public static function getFueraDeTiempo(){
        $objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT encargos.id, encargos.pedido, productos.descripcion, encargos.tiempo_preparacion, encargos.tiempo_realpreparacion from encargos JOIN productos ON productos.id = encargos.producto where tiempo_preparacion < tiempo_realpreparacion ");
        $query->execute();
        $encargos = array();

        while ($fila = $query->fetchObject()){
            array_push($encargos, $fila);
        }

        return $encargos;

    }

    public static function getImporteTotalByPedido($id_pedido){
        $objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT * from encargos JOIN productos ON productos.id = encargos.producto where encargos.pedido = :pedido ");
        $query->bindValue(':pedido', $id_pedido);
        $query->execute();
        $encargos = array();
        $importe = 0;

        while ($fila = $query->fetchObject()){
            $importe += $fila->cantidad * $fila->precio;
        }

        return $importe;
    }



}


?>