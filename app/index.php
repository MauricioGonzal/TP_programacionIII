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
require_once '../vendor/setasign/fpdf/fpdf.php';

require_once './controllers/Producto_controller.php';
require_once './controllers/Usuario_controller.php';
require_once './controllers/Mesa_controller.php';
require_once './controllers/Pedido_controller.php';
require_once './controllers/Encargo_controller.php';
require_once './controllers/Encuesta_controller.php';
require_once './middlewares/VerificadorAcceso.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$app = AppFactory::create();

$app->post('/login', \Usuario_controller::class . ':login');
$app->get('/crear_pdf_login', \Usuario_controller::class . ':crear_pdf_login');
$app->get('/crear_pdf_operaciones', \Usuario_controller::class . ':crear_pdf_operaciones');

//productos
$app->group('/producto', function (RouteCollectorProxy $group) {
	$group->post('[/]', \Producto_controller::class . ':cargarUno');
  	$group->put('[/]', \Producto_controller::class . ':modificarUno');
  	$group->delete('[/]', \Producto_controller::class . ':borrarUno');
	$group->get('/', \Producto_controller::class . ':TraerTodos'); 
  	$group->get('/{id}', \Producto_controller::class . ':TraerUno');
})->add(\VerificadorAcceso::class . ':esSocio');

//usuarios
$app->group('/usuario', function (RouteCollectorProxy $group) {
  	$group->post('[/]', \Usuario_controller::class . ':cargarUno')->add(\VerificadorAcceso::class . ':validarRegistro');
  	$group->put('[/]', \Usuario_controller::class . ':modificarUno');
  	$group->delete('[/]', \Usuario_controller::class . ':borrarUno');
	$group->get('[/]', \Usuario_controller::class . ':TraerTodos');
  	$group->get('/{id}', \Usuario_controller::class . ':TraerUno');
})->add(\VerificadorAcceso::class . ':esSocio');


//mesas
$app->group('/mesa', function (RouteCollectorProxy $group) {
  	$group->post('[/]', \Mesa_controller::class . ':cargarUna');
  	$group->put('[/]', \Mesa_controller::class . ':modificarUno');
  	$group->delete('[/]', \Mesa_controller::class . ':borrarUno');
  	$group->get('/getMasUsada', \Mesa_controller::class . ':getMasUsada');
	$group->get('[/]', \Mesa_controller::class . ':TraerTodos'); 
  	$group->get('/{id}', \Mesa_controller::class . ':TraerUno');
  	$group->post('/cerrarMesa', \Mesa_controller::class . ':cerrar');
})->add(\VerificadorAcceso::class . ':esSocio');

//pedidos - socio
$app->group('/pedido', function (RouteCollectorProxy $group) {
  	$group->get('/getPedidosYDemora', \Pedido_controller::class . ':listarPedidos');
})->add(\VerificadorAcceso::class . ':esSocio');

//pedidos - mozo
$app->group('/pedido', function (RouteCollectorProxy $group) {
	$group->post('[/]', \Pedido_controller::class . ':cargarUno');
  	$group->put('[/]', \Pedido_controller::class . ':modificarUno');
  	$group->delete('[/]', \Pedido_controller::class . ':borrarUno');
  	$group->post('/subirImagen', \Pedido_controller::class . ':subirImagen');
  	$group->get('/listosParaServir', \Pedido_controller::class . ':listarListosParaServir');
	$group->get('[/]', \Pedido_controller::class . ':TraerTodos'); 
  	$group->get('/{id}', \Pedido_controller::class . ':TraerUno');
  	$group->post('/cobrar', \Pedido_controller::class . ':cobrar');
  	$group->post('/servirPedido', \Pedido_controller::class . ':servir');
})->add(\VerificadorAcceso::class . ':esMozo');

//encargos - cocinero - bartender - cervezero
$app->group('/encargo', function (RouteCollectorProxy $group) {
  	$group->post('/tomarEncargo', \Encargo_controller::class . ':tomar');
  	$group->post('/dejarParaServir', \Encargo_controller::class . ':dejarParaServir');
  	$group->get('/listarPendientes', \Encargo_controller::class . ':listarPendientes');
  	$group->get('/listarEnPreparacion', \Encargo_controller::class . ':listarEnPreparacion');
})->add(\VerificadorAcceso::class . ':esEmpleado');

//encargos-socio
$app->group('/encargo', function (RouteCollectorProxy $group) {
  	$group->delete('[/]', \Encargo_controller::class . ':borrarUno');
  	$group->get('/encargosFueraDeTiempo', \Encargo_controller::class . ':encargosFueraDeTiempo');
	$group->get('/', \Encargo_controller::class . ':TraerTodos'); 
  	$group->get('/{id}', \Encargo_controller::class . ':TraerUno');
})->add(\VerificadorAcceso::class . ':esSocio');

//csv
$app->group('/csv', function (RouteCollectorProxy $group) {
  	$group->get('/descargar', \Producto_controller::class . ':descargarDatosCsv');
  	$group->post('/cargar', \Producto_controller::class . ':cargarDatosCsv');
});

//encuestas
$app->group('/encuesta', function (RouteCollectorProxy $group) {
  	$group->get('/listarMejoresComentarios', \Encuesta_controller::class . ':listarMejoresComentarios');
})->add(\VerificadorAcceso::class . ':esSocio');

//cliente
$app->group('/cliente', function (RouteCollectorProxy $group) {
	$group->post('/getTiempoDemora', \Pedido_controller::class . ':obtenerTiempoDemora');
	$group->post('/encuesta', \Encuesta_controller::class . ':cargarUna');
})->add(\VerificadorAcceso::class . ':esCliente');

$app->run();
?>