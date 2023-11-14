<?php 

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once './models/Usuario.php';
require_once './models/Log.php';
class Logguer{

	public function login($request, $handler){

	    $parametros = $request->getParsedBody();
	    $usuario = Usuario::getByUser($parametros['user'], $parametros['password'], $parametros['sector']);
	    if($usuario != false){
	    	$log = Log::crearUno($usuario->id, date('Y-m-d h:i:s'));
	    	Log::insertarUno($log);
	    	return $handler->handle($request);
	    }

	    $respuesta = new Response();
	    $respuesta->getBody()->write("No existe usuario con los datos ingresados");
	    return $respuesta;
	}

}




?>