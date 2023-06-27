<?php
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . './vendor/autoload.php';

require_once './db/AccesoDatos.php';
require_once './controllers/usuarioController.php';
require_once './controllers/productoController.php';
require_once './controllers/sectorController.php';
require_once './controllers/mesaController.php';
require_once './controllers/pedidoController.php';
require_once './controllers/clienteController.php';
// require_once './middlewares/AutentificadorJWT.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();


// Routes
$app->group('/usuarios', function (RouteCollectorProxy $group) {
  $group->post('/alta', \UsuarioController::class . ':CargarUno');
  $group->get('/listar', \UsuarioController::class . ':TraerTodos');
  $group->get('/{rol}', \UsuarioController::class . ':TraerPorRol');
  $group->get('/id/{id}', \UsuarioController::class . ':TraerUno');
  $group->delete('/delete', \UsuarioController::class . ':BorrarUno');
  $group->put('/modificar', \UsuarioController::class . ':ModificarUno');
});

$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->post('/alta', \ProductoController::class . ':CargarUno');
  $group->get('/listar', \ProductoController::class . ':TraerTodos');
  $group->get('/{idSector}', \ProductoController::class . ':TraerPorSector');
  $group->get('/id/{id}', \ProductoController::class . ':TraerUno');
  $group->delete('/delete', \ProductoController::class . ':BorrarUno');
  $group->put('/modificar', \ProductoController::class . ':ModificarUno');
});

$app->group('/sectores', function (RouteCollectorProxy $group) {
  $group->post('/alta', \SectorController::class . ':CargarUno');
  $group->get('/listar', \SectorController::class . ':TraerTodos');
  $group->get('/id/{id}', \SectorController::class . ':TraerUno');
});

$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->post('/alta', \MesaController::class . ':CargarUno');
  $group->get('/listar', \MesaController::class . ':TraerTodos');
  $group->get('/{codMesa}', \MesaController::class . ':TraerUno');
  $group->put('/modificar', \MesaController::class . ':ModificarUno');
});

$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->post('/alta', \PedidoController::class . ':CargarUno');
  $group->get('/listar', \PedidoController::class . ':TraerTodos');
  $group->get('/id/{id}', \PedidoController::class . ':TraerPorId');
  $group->put('/modificar', \PedidoController::class . ':ModificarUno');
  $group->delete('/delete', \PedidoController::class . ':BorrarUno');
});

$app->group('/clientes', function (RouteCollectorProxy $group) {
  $group->post('/alta', \ClienteController::class . ':CargarUno');
  $group->get('/listar', \ClienteController::class . ':TraerTodos');
  $group->get('/id/{id}', \ClienteController::class . ':TraerUno');
  $group->put('/modificar', \ClienteController::class . ':ModificarUno');
  $group->delete('/delete', \ClienteController::class . ':BorrarUno');
});

// $app->group('/login', function (RouteCollectorProxy $group){
//   $group->post('[/]', \UsuarioController::class . ':TraerUno');
// })->add(
//   //aca llamo al mw
// );


// // JWT test routes
// $app->group('/jwt', function (RouteCollectorProxy $group) {

//   $group->post('/crearToken', function (Request $request, Response $response) {    
//     $parametros = $request->getParsedBody();

//     $usuario = $parametros['usuario'];
//     $perfil = $parametros['perfil'];
//     $alias = $parametros['alias'];

//     $datos = array('usuario' => $usuario, 'perfil' => $perfil, 'alias' => $alias);

//     $token = AutentificadorJWT::CrearToken($datos);
//     $payload = json_encode(array('jwt' => $token));

//     $response->getBody()->write($payload);
//     return $response
//       ->withHeader('Content-Type', 'application/json');
//   });

//   $group->get('/devolverPayLoad', function (Request $request, Response $response) {
//     $header = $request->getHeaderLine('Authorization');
//     $token = trim(explode("Bearer", $header)[1]);

//     try {
//       $payload = json_encode(array('payload' => AutentificadorJWT::ObtenerPayLoad($token)));
//     } catch (Exception $e) {
//       $payload = json_encode(array('error' => $e->getMessage()));
//     }

//     $response->getBody()->write($payload);
//     return $response
//       ->withHeader('Content-Type', 'application/json');
//   });

//   $group->get('/devolverDatos', function (Request $request, Response $response) {
//     $header = $request->getHeaderLine('Authorization');
//     $token = trim(explode("Bearer", $header)[1]);

//     try {
//       $payload = json_encode(array('datos' => AutentificadorJWT::ObtenerData($token)));
//     } catch (Exception $e) {
//       $payload = json_encode(array('error' => $e->getMessage()));
//     }

//     $response->getBody()->write($payload);
//     return $response
//       ->withHeader('Content-Type', 'application/json');
//   });

//   $group->get('/verificarToken', function (Request $request, Response $response) {
//     $header = $request->getHeaderLine('Authorization');
//     $token = trim(explode("Bearer", $header)[1]);
//     $esValido = false;

//     try {
//       AutentificadorJWT::verificarToken($token);
//       $esValido = true;
//     } catch (Exception $e) {
//       $payload = json_encode(array('error' => $e->getMessage()));
//     }

//     if ($esValido) {
//       $payload = json_encode(array('valid' => $esValido));
//     }

//     $response->getBody()->write($payload);
//     return $response
//       ->withHeader('Content-Type', 'application/json');
//   });
// });


// $app->get('[/]', function (Request $request, Response $response) {
//     $payload = json_encode(array("mensaje" => "Slim Framework 4 PHP"));
//     $response->getBody()->write($payload);
//     return $response->withHeader('Content-Type', 'application/json');
// });

$app->run();
