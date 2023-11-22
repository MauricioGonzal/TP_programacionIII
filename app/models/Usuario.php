<?php 
require_once './db/AccesoDatos.php';

class Usuario
{
	//Si un usuario no tiene sector es socio

	CONST BORRADO = 0;
	CONST ACTIVO = 1;
	CONST SUSPENDIDO = 2;

	function __construct($nombre, $estado, $sector, $password)
	{
		$this->nombre = $nombre;
		$this->estado = $estado;
		$this->sector = $sector;
		$this->password = $password;
	}

	public static function crearUno($nombre, $estado, $sector, $password){
		$usuario = new Usuario($nombre, $estado, $sector, $password);
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
        $query = $objDataAccess->prepararConsulta("INSERT INTO usuarios (nombre, estado, sector,password) 
        VALUES (:nombre, :estado, :sector, :password)");
        $query->bindValue(':nombre', $usuario->nombre);
        $query->bindValue(':estado', $usuario->estado);
        $query->bindValue(':sector', $usuario->sector);
        $query->bindValue(':password', $usuario->password);
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

	public static function getByUser($user, $password, $sector){
				$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("SELECT * FROM usuarios where nombre=:nombre AND password=:password AND sector=:sector");
        $query->bindValue(':nombre', $user);
        $query->bindValue(':password', $password);
        $query->bindValue(':sector', $sector);
        $query->execute();

        return $query->fetchObject();
	}

	public static function modificar($usuario){
		$objDataAccess = AccesoDatos::obtenerInstancia();
        $query = $objDataAccess->prepararConsulta("UPDATE usuarios SET nombre=:nombre, estado=:estado, sector=:sector, password=:password where id = :id");
        $query->bindValue(':nombre', $usuario->nombre);
        $query->bindValue(':estado', $usuario->estado);
        $query->bindValue(':sector', $usuario->sector);
        $query->bindValue(':password', $usuario->password);
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
}


?>