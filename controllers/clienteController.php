<?php
require_once './models/cliente.php';
require_once './controllers/mesaController.php';

    class ClienteController{
        public function CargarUno($request, $response, $args){
            $params = $request->getParsedBody();
                $client = new Cliente();
                $client->_nombre = $params["nombre"];
                $client->_estado = 0;

                $fecha = new DateTime();
                $client->_fechaIngreso = $fecha->format("Y-m-d H:i:s");
                $client->_codMesa = Mesa::ObtenerMesaLibre();
                $client->_codPedido = null;
                
            $client->CrearCliente();

            $payload = json_encode(array("mensaje" => "Cliente creado con exito, ubicado en la mesa $client->_codMesa"));

            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

        public function TraerUno($request, $response, $args){
            $id = $args['id'];
            $cliente = Cliente::ObtenerCliente($id);
            $payload = json_encode($cliente);
            
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

        public function TraerTodos($request, $response, $args){
            $lista = Cliente::obtenerTodos();
            $payload = json_encode(array("listaCliente" => $lista));

            $response->getBody()->write($payload);

            return $response->withHeader('Content-Type', 'application/json');
        }

        public function ModificarUno($request, $response, $args){
            $data = $request->getParsedBody();
            CLiente::ModificarCliente($data["id"], $data["estado"]);

            $payload = json_encode(array("mensaje" => "Mesa modificado con exito"));

            $response->getBody()->write($payload);
            return $response
              ->withHeader('Content-Type', 'application/json');
        }

        public function BorrarUno($request, $response, $args){
            $data = $request->getParsedBody();
            Cliente::BorrarCliente($data["id"]);
            $payload = json_encode(array("mensaje" => "Cliente borrado con exito"));   
            $response->getBody()->write($payload);

            return $response->withHeader('Content-Type', 'application/json');
        }
    }
?>