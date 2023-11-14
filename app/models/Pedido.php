<?php 
class Pedido{

	function __construct($mesa, $numero, $importe_total, $usuario)
	{
		$this->mesa = $mesa;
		$this->numero = $numero;
		$this->importe_total = $importe_total;
		$this->usuario = $usuario;
	}

	public static function crearUno($mesa, $numero, $importe_total, $usuario){
		$pedido = new Pedido($mesa, $numero, $importe_total, $usuario);
		return $pedido;
	}

	public static function getById($id){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT * FROM pedidos where id = :id");
        $query->bindValue(':id', $id);
        $query->execute();
        return $query->fetchObject();
	}

	public static function insertarUno($pedido){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("INSERT INTO pedidos (mesa, numero, importe_total,usuario) 
        VALUES (:mesa, :numero, :importe_total, :usuario)");
        $query->bindValue(':mesa', $pedido->mesa);
        $query->bindValue(':numero', $pedido->numero);
        $query->bindValue(':importe_total', $pedido->importe_total);
        $query->bindValue(':usuario', $pedido->usuario);
        $query->execute();

        return $objDataAccess->obtenerUltimoId();
	}

	public static function getAll(){
		$pedidos = array();
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT * FROM pedidos");
        $query->execute();
         while ($fila = $query->fetchObject()){
         	array_push($pedidos, $fila);
         }
        return $pedidos;
	}



}



?>