<?php 
class Mesa {

	CONST CERRADA = 1;
	CONST ESPERANDOPEDIDO = 2; 
	CONST COMIENDO = 3;
	CONST PAGANDO = 4;

	function __construct($codigo, $estado)
	{
		$this->codigo = $codigo;
		$this->estado = $estado;
	}

	public static function getCerrada(){
		return SELF::CERRADA;
	}

	public static function getEsperandoPedido(){
		return SELF::ESPERANDOPEDIDO;
	}

	public static function getComiendo(){
		return SELF::COMIENDO;
	}

	public static function getPagando(){
		return SELF::PAGANDO;
	}

	public static function crearUno($codigo, $estado){
		$mesa = new Mesa($codigo, $estado);
		return $mesa;
	}

	public static function getById($id){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT * FROM mesa where id = :id");
        $query->bindValue(':id', $id);
        $query->execute();
        return $query->fetchObject();
	}

	public static function getByCodigo($codigo){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT * FROM mesa where codigo = :codigo");
        $query->bindValue(':codigo', $codigo);
        $query->execute();
        return $query->fetchObject();
	}

	public static function insertarUno($mesa){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("INSERT INTO mesa (codigo, estado) 
        VALUES (:codigo, :estado)");
        $query->bindValue(':codigo', $mesa->codigo);
        $query->bindValue(':estado', $mesa->estado);
        $query->execute();

        return $objDataAccess->obtenerUltimoId();
	}

	public static function getAll(){
		$mesas = array();
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT mesa.id, mesa.codigo, estado_mesa.descripcion FROM mesa JOIN estado_mesa ON mesa.estado = estado_mesa.id");
        $query->execute();
         while ($fila = $query->fetchObject()){
         	array_push($mesas, $fila);
         }
        return $mesas;
	}

	public static function modificar($mesa){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("UPDATE mesa SET codigo=:codigo, estado=:estado where id = :id");
        $query->bindValue(':codigo', $mesa->codigo);
        $query->bindValue(':estado', $mesa->estado);
        $query->bindValue(':id', $mesa->id);
        $query->execute();

        return true;
	}

	public static function borrar($id){
		if(Mesa::getById($id)!=false){
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

	public static function cambiarEstado($codigo, $estado){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("UPDATE mesa SET estado=:estado where codigo = :codigo");
        $query->bindValue(':estado', $estado);
        $query->bindValue(':codigo', $codigo);
        $query->execute();

        return true;
	}

	public static function getMasUsada(){
		
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT mesa, COUNT( mesa ) AS total FROM pedidos GROUP BY mesa ORDER BY total DESC LIMIT 1");
        $query->execute();

        return $query->fetchObject();
	}

}


?>