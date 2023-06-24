<?php
require_once './models/usuario.php';

    class UsuarioController{
        public function CargarUno($request, $response, $args){
            $params = $request->getParsedBody();
            // Creamos el usuario
            // $user = new Usuario($params["rol"], $params["nombre"], $params["dni"]);
                $user = new Usuario();
                $user->_rol = $params["rol"];
                $user->_nombre = $params["nombre"];
                $user->_dni = $params["dni"];
                $user->_estado = 0;
                $user->_fechaRegistro = date("y-m-d");
                $user->_fechaBaja = null;
                
            $user->CrearUsuario();

            $payload = json_encode(array("mensaje" => "Usuario creado con exito"));

            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
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

        public function TraerPorRol($request, $response, $args){
            $rol = $args["rol"];
            // $rol = $request->getAttribute('rol');
            echo $rol;
            // $lista = Usuario::ObtenerPorRol($rol);
            // $payload = json_encode(array("listaUsuario" => $lista));

            // $response->getBody()->write($payload);

            // return $response->withHeader('Content-Type', 'application/json');
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