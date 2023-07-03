<?php
require_once './models/usuario.php';
require_once './models/token.php';

    class UsuarioController{
        public function CargarUno($request, $response, $args){
            $params = $request->getParsedBody();
                $user = new Usuario();
                $user->_rol = $params["rol"];
                $user->_nombre = $params["nombre"];
                $user->_dni = $params["dni"];
                $user->_estado = 0;
                $fecha = new DateTime(date("Y-m-d H:i:s"));
                $user->_fechaRegistro = date_format($fecha, 'Y-m-d H:i:s');
                $user->_fechaBaja = null;
                
            $user->CrearUsuario();

            $payload = json_encode(array("mensaje" => "Usuario creado con exito"));

            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

        public function TraerUno($request, $response, $args){
            //Buscamos usuario por dni
            $user = $args['id'];
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
            $rol = $args['rol'];    
            $lista = Usuario::ObtenerPorRol($rol);
            $payload = json_encode(array("listaUsuario($rol)" => $lista));

            $response->getBody()->write($payload);

            return $response->withHeader('Content-Type', 'application/json');
        }

        public function ModificarUno($request, $response, $args){
            $data = $request->getParsedBody();
            Usuario::modificarUsuario($data["id"], $data["estado"], $data["fechaBaja"]);

            $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

            $response->getBody()->write($payload);
            return $response
              ->withHeader('Content-Type', 'application/json');
        }

        public function BorrarUno($request, $response, $args){
            $data = $request->getParsedBody();

            $usuario = Usuario::ObtenerUsuario($data["id"]);
            if($usuario->_fechaBaja == null){
                Usuario::borrarUsuario($data["id"]);
                $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));   
            }
            else{
                $payload = json_encode(array("mensaje" => "El Usuario fue borrado con anterioridad"));
            }
            $response->getBody()->write($payload);

            return $response->withHeader('Content-Type', 'application/json');
        }

        public function Login($request, $response, $args){
            $params = $request->getParsedBody();
            
            $aux = Usuario::ObtenerUsuarioActivo($params["nombre"], $params["dni"]);
            if($aux !== null){
                $token = Token::CrearToken($aux->_id, $aux->_rol, $params["admin"]);
                $payload = json_encode(array("mensaje" => "OK. $aux->_rol", "token" => $token));
            }
            else{
                $payload = json_encode(array("mensaje" => "ERROR en el ingreso de las credenciales"));
            }
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }
?>