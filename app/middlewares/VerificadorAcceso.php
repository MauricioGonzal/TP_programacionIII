<?php 

require_once './models/AutentificadorToken.php';
require_once './models/Usuario.php';
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
                $usuario = Usuario::getByUser($data->usuario);

                if($usuario != false && password_verify($data->password, $usuario->password)){
                    $response = $next->handle($request);
                }
                else $response->getBody()->write(json_encode(array("msj" => "El usuario no tiene los permisos necesarios para realizar esta accion")));
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


	public function esRolOperador($request, $next){
		$header = $request->getHeaderLine('Authorization');
        $response = new Response();
        if (!empty($header)) {
            $token = trim(explode("Bearer", $header)[1]);
            AutentificadorToken::VerificarToken($token);
            $data = AutentificadorToken::obtenerPayload($token)->data;
            if(strtoupper($data->rol) == 'OPERADOR') $response = $next->handle($request);
            else $response->getBody()->write(json_encode(array("msj" => "El usuario no tiene los permisos necesarios para realizar esta accion")));
            
        } else {
            $response->getBody()->write(json_encode(array("Token error" => "Es necesario un token")));
            $response = $response->withStatus(401);
        }
        return  $response->withHeader('Content-Type', 'application/json');
	}

    public function esRolSupervisor($request, $next){
        $header = $request->getHeaderLine('Authorization');
        $response = new Response();
        if (!empty($header)) {
            $token = trim(explode("Bearer", $header)[1]);
            AutentificadorToken::VerificarToken($token);
            $data = AutentificadorToken::obtenerPayload($token)->data;
            if(strtoupper($data->rol) == 'SUPERVISOR') $response = $next->handle($request);
            else $response->getBody()->write(json_encode(array("msj" => "El usuario no tiene los permisos necesarios para realizar esta accion")));
            
        } else {
            $response->getBody()->write(json_encode(array("Token error" => "Es necesario un token")));
            $response = $response->withStatus(401);
        }
        return  $response->withHeader('Content-Type', 'application/json');
    }
}

?>