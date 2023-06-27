<?php
require_once './models/pedido.php';
require_once './models/producto.php';

    class PedidoController{

        public function CargarUno($request, $response, $args){
            $params = $request->getParsedBody();
            //Debería de validar que no exista un pedido igual(mismo id pedido y producto)
            $aux = Pedido::ObtenerPedido($params["id"], $params["idProducto"]);
            
            if($aux != null){
                Pedido::ModificarPedido($aux->_id, $aux->_idProducto, "cantidad", $params["cantidad"] + $aux->_cantidad);
                $payload = json_encode(array("mensaje" => "Pedido ya registrado. Se acumularon las cantidades"));
            }
            else{
                $pedido = new Pedido();
                $pedido->_id = $params["id"];
                $pedido->_idProducto = $params["idProducto"];
                $pedido->_cantidad = $params["cantidad"];
                $pedido->_estado = 0;
                
                $fecha = new DateTime();
                $pedido->_fechaInicio = $fecha->format("Y-m-d H:i:s");

                $auxTiempoEstimado = Producto::ObtenerProducto($pedido->_idProducto)->_tiempoPreparado;
                
                $pedido->_fechaEstimadaFinal = date_modify($fecha, "+$auxTiempoEstimado minutes");                 
                $pedido->CrearPedido();
                $payload = json_encode(array("mensaje" => "Pedido creado con exito"));
            }

            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

        public function TraerTodos($request, $response, $args){
            $lista = Pedido::ObtenerTodos();
            $payload = json_encode(array("listaPedidos" => $lista));

            $response->getBody()->write($payload);

            return $response->withHeader('Content-Type', 'application/json');
        }

        public function TraerPorId($request, $response, $args){
            $pedidos = $args['id'];    
            $lista = Pedido::ObtenerPorId($pedidos);
            $payload = json_encode(array("listaPedidos($pedidos)" => $lista));

            $response->getBody()->write($payload);

            return $response->withHeader('Content-Type', 'application/json');
        }

        public function ModificarUno($request, $response, $args){
            $data = $request->getParsedBody();
            Pedido::modificarPedido($data["id"], $data["idProducto"], $data["variable"], $data["valor"]);

            $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));

            $response->getBody()->write($payload);
            return $response
              ->withHeader('Content-Type', 'application/json');
        }

        public function BorrarUno($request, $response, $args){
            $data = $request->getParsedBody();
            Pedido::borrarPedido($data["id"], $data["idProducto"]);
            $payload = json_encode(array("mensaje" => "Pedido borrado con exito"));   
            $response->getBody()->write($payload);

            return $response->withHeader('Content-Type', 'application/json');
        }
    }
?>