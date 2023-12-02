<?php 
require_once './db/AccesoDatos.php';

class Usuario
{
	CONST BORRADO = 0;
	CONST ACTIVO = 1;
	CONST SUSPENDIDO = 2;

	function __construct($nombre, $estado, $sector, $password, $documento)
	{
		$this->nombre = $nombre;
		$this->estado = $estado;
		$this->sector = $sector;
		$this->password = $password;
		$this->documento = $documento;
	}

	public static function getActivo(){
		return SELF::ACTIVO;
	}

	public static function getSuspendido(){
		return SELF::SUSPENDIDO;
	}

	public static function getBorrado(){
		return SELF::BORRADO;
	}

	public static function crearUno($nombre, $estado, $sector, $password, $documento){
		$password = password_hash($password, PASSWORD_BCRYPT);
		$usuario = new Usuario($nombre, $estado, $sector, $password, $documento);
		return $usuario;
	}

	public static function getById($id){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT * FROM usuarios where id = :id");
        $query->bindValue(':id', $id);
        $query->execute();
        return $query->fetchObject();
	}

	public static function insertarUno($usuario){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("INSERT INTO usuarios (nombre, estado, sector,password, documento) 
        VALUES (:nombre, :estado, :sector, :password, :documento)");
        $query->bindValue(':nombre', $usuario->nombre);
        $query->bindValue(':estado', $usuario->estado);
        $query->bindValue(':sector', $usuario->sector);
        $query->bindValue(':password', $usuario->password);
        $query->bindValue(':documento', $usuario->documento);
        $query->execute();

        return $objDataAccess->obtenerUltimoId();
	}

	public static function getAll(){
		$usuarios = array();
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT * FROM usuarios");
        $query->execute();
         while ($fila = $query->fetchObject()){
         	array_push($usuarios, $fila);
         }
        return $usuarios;
	}

	public static function getByUser($user, $documento, $sector){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT * FROM usuarios where nombre=:nombre AND documento=:documento AND sector=:sector");
        $query->bindValue(':nombre', $user);
        $query->bindValue(':sector', $sector);
        $query->bindValue(':documento', $documento);
        $query->execute();

        return $query->fetchObject();
	}

	public static function modificar($usuario){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("UPDATE usuarios SET nombre=:nombre, estado=:estado, sector=:sector, password=:password, documento=:documento where id = :id");
        $query->bindValue(':nombre', $usuario->nombre);
        $query->bindValue(':estado', $usuario->estado);
        $query->bindValue(':sector', $usuario->sector);
        $query->bindValue(':password', $usuario->password);
        $query->bindValue(':documento', $usuario->documento);
        $query->bindValue(':id', $usuario->id);
        $query->execute();

        return true;
	}

	public static function borrar($id){
		if(Usuario::getById($id)!=false){
			$objDataAccess = AccesoDatos::obtenerInstancia();
	        $query = $objDataAccess->prepararConsulta("DELETE FROM usuarios where id = :id");
	        $query->bindValue(':id', $id);
	        $query->execute();

	        return true;
		}
		else{
			return false;
		}
		
	}

	public static function esSocio($sector){
		return $sector == 0;
	}

	public static function getByDocumento($documento){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT * FROM usuarios where documento = :documento");
        $query->bindValue(':documento', $documento);
        $query->execute();

        return $query->fetchObject();

	}
}


?>