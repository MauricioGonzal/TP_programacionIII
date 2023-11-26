<?php 

require_once './models/AutentificadorToken.php';
require_once './models/Usuario.php';
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class VerificadorToken{

	public function validarRequest($request, $next){
		$header = $request->getHeaderLine('Authorization');
        $response = new Response();
        if (!empty($header)) {
            $token = trim(explode("Bearer", $header)[1]);
            AutentificadorToken::VerificarToken($token);
            $data = AutentificadorToken::obtenerPayload($token)->data;
            if(Usuario::esSocio($data->sector)) $response = $next->handle($request);
            else $response->getBody()->write(json_encode(array("msj" => "El usuario no tiene los permisos necesarios para realizar esta accion")));
            
        } else {
            $response->getBody()->write(json_encode(array("Token error" => "Es necesario un token")));
            $response = $response->withStatus(401);
        }
        return  $response->withHeader('Content-Type', 'application/json');
	}





}

?>