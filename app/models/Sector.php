<?php 

class Sector{

//Si no tiene sector es cliente.

	CONST CLIENTE = 0;
	CONST SOCIO = 1;
	CONST BARTENDER = 2;
	CONST CERVEZERO = 3;
	CONST COCINERO = 4;
	CONST MOZO = 5;


	public static function getSector($sector){
		$sector = strtolower($sector);

		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT * FROM sectores where nombre = :nombre");
        $query->bindValue(':nombre', $sector);
        $query->execute();

        return $query->fetchObject();
	}

	public static function getSocio(){
		return SELF::SOCIO;
	}

	public static function getBartender(){
		return SELF::BARTENDER;
	}

	public static function getCervezero(){
		return SELF::CERVEZERO;
	}

	public static function getCocinero(){
		return SELF::COCINERO;
	}

	public static function getMozo(){
		return SELF::MOZO;
	}

	public static function getCliente(){
		return SELF::CLIENTE;
	}


}


?>