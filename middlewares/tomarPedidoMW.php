<?php
use Dotenv\Loader\Resolver;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once './models/token.php';

    class TomarPedidoMW{
        public function __invoke(Request $request,RequestHandler $handler) : Response{
            try{
                $header = $request->getHeaderLine('Authorization');
                if(!empty($header)){
                    $token = trim(explode("Bearer", $header)[1]);
                    $rol = Token::ObtenerRol($token);
                    if($rol == 'Mozo' || $rol == "Socio"){
                        json_encode(array('datos' => Token::VerificarToken($token)));
                        $idUser = Token::ObtenerIdUsuario($token);   
                        echo "Token validado: \n idUsuario = $idUser\n";
                        return $handler->handle($request);
                    }
                    throw new Exception("Usuario no autorizado");
                }
                else{                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                
                    throw new Exception("Token vacío");
                }
            } 
            catch(\Throwable $th){
                $response = new Response();
                $payload = json_encode(array("mensaje" => "ERROR, ".$th->getMessage()));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');;
            }
        }
    }
?>