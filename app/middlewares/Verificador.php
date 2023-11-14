<?php 

use Slim\Psr7\Response;

require_once './models/Usuario.php';
class Verificador{
	//si el usuario tiene sector 0 es socio
	public function VerificarRango($request, $handler){
		$params = $request->getParsedBody();
		$usuario = Usuario::getById($params['id_usuario']);
		if($usuario != false && $usuario->sector == 0){
			return $handler->handle($request);
		}
		else{
		    $respuesta = new Response();
	    	$respuesta->getBody()->write("El usuario no tiene los permisos correspondientes");
	    	return $respuesta;
		}
	}
}


?>