<?php 

class Sector{

	public static function getSector($sector){
		$sector = strtolower($sector);

		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT * FROM sectores where nombre = :nombre");
        $query->bindValue(':nombre', $sector);
        $query->execute();

        return $query->fetchObject();
	}


}


?>