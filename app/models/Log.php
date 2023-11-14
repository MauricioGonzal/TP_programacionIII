<?php 
class Log{

	function __construct($usuario, $fecha)
	{
		$this->usuario = $usuario;
		$this->fecha = $fecha;
	}

	public static function crearUno($usuario, $fecha){
		$log = new Log($usuario, $fecha);
		return $log;
	}

	public static function getById($id){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT * FROM logs where id = :id");
        $query->bindValue(':id', $id);
        $query->execute();
        return $query->fetchObject();
	}

	public static function insertarUno($log){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("INSERT INTO logs (usuario, fecha) 
        VALUES (:usuario, :fecha)");
        $query->bindValue(':usuario', $log->usuario);
        $query->bindValue(':fecha', $log->fecha);
        $query->execute();

        return $objDataAccess->obtenerUltimoId();
	}

	public static function getAll(){
		$logs = array();
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT * FROM logs");
        $query->execute();
         while ($fila = $query->fetchObject()){
         	array_push($logs, $fila);
         }
        return $logs;
	}



	}

?>