<?php 
class Encuesta {

	function __construct($id_pedido, $puntuacion_mesa, $puntuacion_restaurante, $puntuacion_mozo, $puntuacion_cocinero, $texto)
	{
		$this->id_pedido = $id_pedido;
		$this->puntuacion_mesa = $puntuacion_mesa;
		$this->puntuacion_restaurante = $puntuacion_restaurante;
		$this->puntuacion_mozo = $puntuacion_mozo;
		$this->puntuacion_cocinero = $puntuacion_cocinero;
		$this->texto = $texto;
	}

	public static function crearUno($id_pedido, $puntuacion_mesa, $puntuacion_restaurante, $puntuacion_mozo, $puntuacion_cocinero, $texto){
		$encuesta = new Encuesta($id_pedido, $puntuacion_mesa, $puntuacion_restaurante, $puntuacion_mozo, $puntuacion_cocinero, $texto);
		return $encuesta;
	}

	public static function getById($id){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT * FROM encuesta where id = :id");
        $query->bindValue(':id', $id);
        $query->execute();
        return $query->fetchObject();
	}

	public static function insertarUno($encuesta){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("INSERT INTO encuesta (id_pedido, puntuacion_mesa, puntuacion_restaurante, puntuacion_mozo, puntuacion_cocinero, texto) 
        VALUES (:id_pedido, :puntuacion_mesa, :puntuacion_restaurante, :puntuacion_mozo, :puntuacion_cocinero, :texto)");
        $query->bindValue(':id_pedido', $encuesta->id_pedido);
        $query->bindValue(':puntuacion_mesa', $encuesta->puntuacion_mesa);
        $query->bindValue(':puntuacion_restaurante', $encuesta->puntuacion_restaurante);
        $query->bindValue(':puntuacion_mozo', $encuesta->puntuacion_mozo);
        $query->bindValue(':puntuacion_cocinero', $encuesta->puntuacion_cocinero);
        $query->bindValue(':texto', $encuesta->texto);
        $query->execute();

        return $objDataAccess->obtenerUltimoId();
	}

	public static function getAll(){
		$encuestas = array();
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT * FROM encuesta");
        $query->execute();
         while ($fila = $query->fetchObject()){
         	array_push($encuestas, $fila);
         }
        return $encuestas;
	}

	public static function modificar($mesa){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("UPDATE encuesta SET codigo=:codigo, estado=:estado where id = :id");
        $query->bindValue(':codigo', $mesa->codigo);
        $query->bindValue(':estado', $mesa->estado);
        $query->bindValue(':id', $mesa->id);
        $query->execute();

        return true;
	}

	public static function borrar($id){
		if(Mesa::getById($id)!=false){
			$objDataAccess = AccesoDatos::obtenerInstancia();
	        $query = $objDataAccess->prepararConsulta("DELETE FROM encuesta where id = :id");
	        $query->bindValue(':id', $id);
	        $query->execute();
	        return true;
		}
		else{
			return false;
		}
	}

	public static function getMejores(){
		$encuestas = array();
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT encuesta.id, (sum(puntuacion_mesa) + sum(puntuacion_restaurante) + sum(puntuacion_mozo) + sum(puntuacion_cocinero))/4 AS promedio FROM encuesta GROUP BY id ORDER BY promedio DESC LIMIT 3");
        $query->execute();
         while ($fila = $query->fetchObject()){
         	array_push($encuestas, $fila);
         }
        return $encuestas;
		
	}

}


?>