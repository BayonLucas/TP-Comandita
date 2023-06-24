<?php
require_once './models/usuario.php';

    class UsuarioController extends Usuario{
        public function CargarUno($request, $response, $args){
            $params = $request->getParsedBody();
            var_dump($params);
            // Creamos el usuario
            // $user = new Usuario($params["rol"], $params["nombre"], $params["dni"]);
            // $user->CrearUsuario();

            // $payload = json_encode(array("mensaje" => "Usuario creado con exito"));

            // $response->getBody()->write($payload);
            // return $response->withHeader('Content-Type', 'application/json');
        }

        public function TraerUno($request, $response, $args){
            //Buscamos usuario por dni
            $user = $args['dni'];
            $usuario = Usuario::obtenerUsuario($user);
            $payload = json_encode($usuario);
            
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

        public function TraerTodos($request, $response, $args){
            $lista = Usuario::obtenerTodos();
            $payload = json_encode(array("listaUsuario" => $lista));

            $response->getBody()->write($payload);

            return $response->withHeader('Content-Type', 'application/json');
        }

        // public function ModificarUno($request, $response, $args)
        // {
        //     $parametros = $request->getParsedBody();

        //     $nombre = $parametros['nombre'];
        //     Usuario::modificarUsuario($nombre);

        //     $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

        //     $response->getBody()->write($payload);
        //     return $response
        //       ->withHeader('Content-Type', 'application/json');
        // }

        public function BorrarUno($request, $response, $args){
            $params = $request->getParsedBody();

            $usuarioId = $params['id'];
            Usuario::borrarUsuario($usuarioId);

            $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));
            $response->getBody()->write($payload);

            return $response->withHeader('Content-Type', 'application/json');
        }
    }
?>