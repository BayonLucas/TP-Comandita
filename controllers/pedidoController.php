<?php
require_once './models/pedido.php';

    class PedidoController{
        public function CargarUno($request, $response, $args){
            $params = $request->getParsedBody();
            // Creamos el Pedido
                $pedido = new Pedido();
                $pedido->_idSector = $params["idSector"];
                $pedido->_nombre = $params["nombre"];
                $pedido->_precio = $params["precio"];
                $pedido->_tiempoPreparado = $params["tiempoPreparado"];                
            $pedido->CrearPedido();

            $payload = json_encode(array("mensaje" => "Pedido creado con exito"));

            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

        public function TraerUno($request, $response, $args){
            //Buscamos Pedido por id
            $id = $args['id'];
            $aux = Pedido::obtenerPedido($id);
            $payload = json_encode($aux);
            
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

        public function TraerTodos($request, $response, $args){
            $lista = Pedido::ObtenerTodos();
            $payload = json_encode(array("listaPedidos" => $lista));

            $response->getBody()->write($payload);

            return $response->withHeader('Content-Type', 'application/json');
        }

        public function TraerPorSector($request, $response, $args){
            $sector = $args['idSector'];    
            $lista = Pedido::ObtenerPorSector($sector);
            $payload = json_encode(array("listaPedidos($sector)" => $lista));

            $response->getBody()->write($payload);

            return $response->withHeader('Content-Type', 'application/json');
        }

        public function ModificarUno($request, $response, $args){
            $data = $request->getParsedBody();
            Pedido::modificarPedido($data["id"], $data["precio"]);

            $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));

            $response->getBody()->write($payload);
            return $response
              ->withHeader('Content-Type', 'application/json');
        }

        public function BorrarUno($request, $response, $args){
            $data = $request->getParsedBody();
            Pedido::borrarPedido($data["id"]);
            $payload = json_encode(array("mensaje" => "Pedido borrado con exito"));   
            $response->getBody()->write($payload);

            return $response->withHeader('Content-Type', 'application/json');
        }
    }
?>