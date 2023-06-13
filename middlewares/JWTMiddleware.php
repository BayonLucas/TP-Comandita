<?php
include_once 'AutenticadorJWT.php';

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;


    class Logger{

        public function ValidarLog(Request $request, RequestHandler $handler){
            //Agarrar el usuario recibido por request
            $datosUsuario = $request->getParsedBody();
            $token = $request->getHeaderLine('Authorization');

            $token = AutentificadorJWT::VerificarToken($token);
            //llamo al request handler
            $lista = Usuario::obtenerTodos();
            $payload = json_encode(array("listaUsuario" => $lista));
    
            $response->getBody()->write($payload);
            return $response
              ->withHeader('Content-Type', 'application/json');
            }



            $resp = new Response();

            return $resp;
        }


    } 





?>