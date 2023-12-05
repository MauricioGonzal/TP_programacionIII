<?php 
class Log_operacion{

	function __construct($usuario, $accion, $id_pedido, $id_encargo, $fecha)
	{
		$this->usuario = $usuario;
		$this->accion = $accion;
		$this->id_pedido = $id_pedido;
		$this->id_encargo = $id_encargo;
		$this->fecha = $fecha;
	}

	public static function crearUno($usuario, $accion, $id_pedido, $id_encargo){
		$log = new Log_operacion($usuario, $accion, $id_pedido, $id_encargo, date('d-m-Y h:i:s', time()));
		return $log;
	}

	public static function getById($id){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT * FROM log_operacion where id = :id");
        $query->bindValue(':id', $id);
        $query->execute();
        return $query->fetchObject();
	}

	public static function insertarUno($log){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("INSERT INTO log_operacion (usuario, accion, id_pedido, id_encargo, fecha) 
        VALUES (:usuario, :accion, :id_pedido, :id_encargo, :fecha)");
        $query->bindValue(':usuario', $log->usuario);
        $query->bindValue(':accion', $log->accion);
        $query->bindValue(':id_pedido', $log->id_pedido);
        $query->bindValue(':id_encargo', $log->id_encargo);
        $query->bindValue(':fecha', $log->fecha);
        $query->execute();

        return $objDataAccess->obtenerUltimoId();
	}

	public static function getAll(){
		$logs = array();
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT * FROM log_operacion");
        $query->execute();
         while ($fila = $query->fetchObject()){
         	array_push($logs, $fila);
         }
        return $logs;
	}



	}

?>