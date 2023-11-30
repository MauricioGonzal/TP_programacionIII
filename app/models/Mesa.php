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
        $query = $objDataAccess->prepararConsulta("SELECT * FROM mesa");
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

	public static function cambiarEstado($id_mesa, $estado){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("UPDATE mesa SET estado=:estado where id = :id");
        $query->bindValue(':estado', $estado);
        $query->bindValue(':id', $id_mesa);
        $query->execute();

        return true;
	} 

}


?>