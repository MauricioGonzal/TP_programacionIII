<?php 

require_once './models/AutentificadorToken.php';
require_once './models/Usuario.php';
require_once './models/Sector.php';

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class VerificadorAcceso{

    public function validarIngreso($request, $next){
        $header = $request->getHeaderLine('Authorization');
        $response = new Response();
        if (!empty($header)) {
            try{
                $token = trim(explode("Bearer", $header)[1]);
                AutentificadorToken::VerificarToken($token);
                $data = AutentificadorToken::obtenerPayload($token)->data;
                $usuario = Usuario::getByUser($data->usuario, $data->documento, $data->sector);

                if($usuario != false && password_verify($data->password, $usuario->password)){
                    $response = $next->handle($request);
                }
                else $response->getBody()->write(json_encode(array("msj" => "ERROR EN TOKEN. Datos invalidos. Volver a ingresar")));
                }
                catch(Exception $e){
                    $response->getBody()->write(json_encode(array("Token error" => "ERROR. No esta registrado. Vuelva a ingresar")));
                }
                
            
        } else {
            $response->getBody()->write(json_encode(array("Token error" => "Es necesario un token")));
            $response = $response->withStatus(401);
        }
        return  $response->withHeader('Content-Type', 'application/json');
    }


	public function esSocio($request, $next){
		$header = $request->getHeaderLine('Authorization');
        $response = new Response();
        if (!empty($header)) {
            $token = trim(explode("Bearer", $header)[1]);
            AutentificadorToken::VerificarToken($token);
            $data = AutentificadorToken::obtenerPayload($token)->data;
            if(strtoupper($data->sector) == Sector::getSocio()) $response = $next->handle($request);
            else $response->getBody()->write(json_encode(array("msj" => "El usuario no tiene los permisos necesarios para realizar esta accion")));
            
        } else {
            $response->getBody()->write(json_encode(array("Token error" => "Es necesario un token")));
            $response = $response->withStatus(401);
        }
        return  $response->withHeader('Content-Type', 'application/json');
	}

    public function esMozo($request, $next){
        $header = $request->getHeaderLine('Authorization');
        $response = new Response();
        if (!empty($header)) {
            $token = trim(explode("Bearer", $header)[1]);
            AutentificadorToken::VerificarToken($token);
            $data = AutentificadorToken::obtenerPayload($token)->data;
            if(strtoupper($data->sector) ==  Sector::getMozo()){
                $response = $next->handle($request);
            } 
            else $response->getBody()->write(json_encode(array("msj" => "El usuario no tiene los permisos necesarios para realizar esta accion")));
            
        } else {
            $response->getBody()->write(json_encode(array("Token error" => "Es necesario un token")));
            $response = $response->withStatus(401);
        }
        return  $response->withHeader('Content-Type', 'application/json');
    }

    public function esCliente($request, $next){
        $header = $request->getHeaderLine('Authorization');
        $response = new Response();
        if (!empty($header)) {
            $token = trim(explode("Bearer", $header)[1]);
            AutentificadorToken::VerificarToken($token);
            $data = AutentificadorToken::obtenerPayload($token)->data;
            if(strtoupper($data->sector) ==  Sector::getCliente()) $response = $next->handle($request);
            else $response->getBody()->write(json_encode(array("msj" => "El usuario no tiene los permisos necesarios para realizar esta accion")));
            
        } else {
            $response->getBody()->write(json_encode(array("Token error" => "Es necesario un token")));
            $response = $response->withStatus(401);
        }
        return  $response->withHeader('Content-Type', 'application/json');
    }

    public function esEmpleado($request, $next){
        $header = $request->getHeaderLine('Authorization');
        $response = new Response();
        if (!empty($header)) {
            $token = trim(explode("Bearer", $header)[1]);
            AutentificadorToken::VerificarToken($token);
            $data = AutentificadorToken::obtenerPayload($token)->data;
            if(strtoupper($data->sector) ==  Sector::getCocinero() || strtoupper($data->sector) ==  Sector::getBartender() || strtoupper($data->sector) ==  Sector::getCervezero()) $response = $next->handle($request);
            else $response->getBody()->write(json_encode(array("msj" => "El usuario no tiene los permisos necesarios para realizar esta accion")));
            
        } else {
            $response->getBody()->write(json_encode(array("Token error" => "Es necesario un token")));
            $response = $response->withStatus(401);
        }
        return  $response->withHeader('Content-Type', 'application/json');
    }

    public function validarRegistro($request, $next){
        $response = new Response();
        $params = $request->getParsedBody();
        if (Usuario::getByDocumento($params['documento']) != false){
            $response->getBody()->write(json_encode(array("msg" => "Ya existe un usuario con el documento ingresado."))); 
        } else {
           $response = $next->handle($request);
        }
        return  $response->withHeader('Content-Type', 'application/json');
    }
}

?>