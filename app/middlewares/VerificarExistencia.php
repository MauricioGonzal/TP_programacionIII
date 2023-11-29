<?php 

require_once './models/AutentificadorToken.php';
require_once './models/Usuario.php';
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class VerificarExistencia{

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