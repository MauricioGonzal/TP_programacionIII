<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;
require_once '../vendor/autoload.php';

require_once './controllers/Producto_controller.php';
require_once './controllers/Usuario_controller.php';
require_once './controllers/Mesa_controller.php';
require_once './controllers/Pedido_controller.php';
require_once './controllers/Encargo_controller.php';
require_once './middlewares/VerificarExistencia.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$app = AppFactory::create();

$app->post('/login', \Usuario_controller::class . ':login');

//productos
$app->group('/producto', function (RouteCollectorProxy $group) {
	$group->post('[/]', \Producto_controller::class . ':cargarUno');
  	$group->put('[/]', \Producto_controller::class . ':modificarUno');
  	$group->delete('[/]', \Producto_controller::class . ':borrarUno');
	$group->get('/', \Producto_controller::class . ':TraerTodos'); 
  	$group->get('/{id}', \Producto_controller::class . ':TraerUno');
});

//usuarios
$app->group('/usuario', function (RouteCollectorProxy $group) {
  	$group->post('[/]', \Usuario_controller::class . ':cargarUno')->add(\VerificarExistencia::class . ':validarRegistro');
  	$group->put('[/]', \Usuario_controller::class . ':modificarUno');
  	$group->delete('[/]', \Usuario_controller::class . ':borrarUno');
	$group->get('[/]', \Usuario_controller::class . ':TraerTodos');
  	$group->get('/{id}', \Usuario_controller::class . ':TraerUno');
  	$group->post('/listarPendientes', \Usuario_controller::class . ':listarPedidos');
});

//mesas
$app->group('/mesa', function (RouteCollectorProxy $group) {
  	$group->post('[/]', \Mesa_controller::class . ':cargarUna');
  	$group->put('[/]', \Mesa_controller::class . ':modificarUno');
  	$group->delete('[/]', \Mesa_controller::class . ':borrarUno');
	$group->get('[/]', \Mesa_controller::class . ':TraerTodos'); 
  	$group->get('/{id}', \Mesa_controller::class . ':TraerUno');
});

//pedidos
$app->group('/pedido', function (RouteCollectorProxy $group) {
	$group->post('[/]', \Pedido_controller::class . ':cargarUno');
  	$group->put('[/]', \Pedido_controller::class . ':modificarUno');
  	$group->delete('[/]', \Pedido_controller::class . ':borrarUno');
	$group->get('[/]', \Pedido_controller::class . ':TraerTodos'); 
  	$group->get('/{id}', \Pedido_controller::class . ':TraerUno');
});

//encargos
$app->group('/encargo', function (RouteCollectorProxy $group) {
  	$group->post('/tomarEncargo', \Encargo_controller::class . ':tomarEncargo');
});

//csv
$app->group('/csv', function (RouteCollectorProxy $group) {
  	$group->get('/descargar', \Producto_controller::class . ':descargarDatosCsv');
  	$group->post('/cargar', \Producto_controller::class . ':cargarDatosCsv');
});
$app->run();
?>