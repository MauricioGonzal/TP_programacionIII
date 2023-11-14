<?php 
class Mesa {

	CONST CERRADA = 0;
	CONST ESPERANDOPEDIDO = 1; 
	CONST COMIENDO = 2;
	CONST PAGANDO = 3;

	function __construct($codigo, $estado)
	{
		$this->codigo = $codigo;
		$this->estado = $estado;
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
        $query = $objDataAccess->prepararConsulta("SELECT * FROM mesa");
        $query->execute();
         while ($fila = $query->fetchObject()){
         	array_push($mesas, $fila);
         }
        return $mesas;
	}




}


?>