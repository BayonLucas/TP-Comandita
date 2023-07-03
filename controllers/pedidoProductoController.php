<?php
    require_once './models/pedido_producto.php';
    require_once './models/pedido.php';
    require_once './db/picture.php';

    class Pedido_ProductoController{
        public function CargarUno($request, $response, $args){
            $params = $request->getParsedBody();
            $ppInfo = $params[0];
            $fecha = new DateTime(date("Y-m-d H:i:s"));

            //Creo la estructura del Pedido_producto
            $pp = new Pedido_Producto();
            $pp->_idUsuario = $ppInfo["idUsuario"]; //Obtener desde el MW
            $pp->_idCliente = $ppInfo["idCliente"];
            $pp->_idMesa = Pedido_Producto::ObtenerIdMesa($pp->_idCliente);
            $pp->_codPedido = Pedido_Producto::ObtenerCodPedidoNuevo(Pedido_Producto::ObtenerTodos());
            $pp->_codMesa = Pedido_Producto::ObtenerCodMesa($pp->_idCliente);
            $pp->_estado = 0;
            $pp->_fechaIngreso = date_format($fecha, 'Y-m-d H:i:s');
            $pp->_fechaFinalizado = null;
            $pp->_fotoCliente = null;
            $pp->_importeTotal = 0;
            $pp->_tiempoTotalEspera = 0;
            $pp->_fechaAnulado = null;
            $idPP = $pp->CrearPedido_Producto();

            //Levantar pedidos adjuntados en el body
            $auxTiempoMax = 0;
            for($i = 1; $i < count($params); $i++){
                $pedido = new Pedido();
                $pedido->_id = $idPP; //Esto no existe. No puedo usar su valor
                $pedido->_idProducto = $params[$i]["idProducto"];
                $pedido->_cantidad = $params[$i]["cantidad"];
                $pedido->_estado = 0;
                $pedido->_fechaInicio = $pp->_fechaIngreso;
                $auxTiempoEstimado = Producto::ObtenerProducto($pedido->_idProducto)->_tiempoPreparado;
                $pedido->_fechaEstimadaFinal = date_modify($fecha, "+$auxTiempoEstimado minutes");     
                                            
                $pedido->CrearPedido();

                if($auxTiempoMax < $auxTiempoEstimado){
                    $auxTiempoMax = $auxTiempoEstimado;
                }
            }
            $tiempoTotalEspera = $auxTiempoMax + 10;
            $importeTotal = Pedido_Producto::ObtenerPrecioTotal($idPP);;

            //Modifico al pedido_Producto para agregar los datos finales salvo la imagen
            $arr = array("_tiempoTotalEspera" => $tiempoTotalEspera, "_importeTotal" => $importeTotal);
            Pedido_Producto::Modificar($idPP, $arr);

            $payload = json_encode(array("mensaje" => "Pedido creado con exito"));
            
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
        
        public function TraerTodos($request, $response, $args){
            $lista = Pedido_Producto::ObtenerTodos();
            $payload = json_encode(array("listaPedidos" => $lista));

            $response->getBody()->write($payload);

            return $response->withHeader('Content-Type', 'application/json');
        }

        public function TraerPorId($request, $response, $args){
            $id = $args['id'];    
            $lista = Pedido_Producto::ObtenerPorId($id);
            $payload = json_encode(array("listaPedidos($id)" => $lista));

            $response->getBody()->write($payload);

            return $response->withHeader('Content-Type', 'application/json');
        }

        public function ModificarUno($request, $response, $args){
            $id = $args['id'];    
            $data = $request->getParsedBody();

            Pedido_Producto::Modificar($id, $data);

            $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));

            $response->getBody()->write($payload);
            return $response
              ->withHeader('Content-Type', 'application/json');
        }

        public function BorrarUno($request, $response, $args){
            $data = $request->getParsedBody();
            Pedido_Producto::BorrarPedidoProducto($data["id"]);
            $payload = json_encode(array("mensaje" => "Pedido borrado con exito"));   
            $response->getBody()->write($payload);

            return $response->withHeader('Content-Type', 'application/json');
        }

        public function TomarFoto($request, $response, $args){
            $data = $request->getParsedBody(); //id y foto
            $auxPP = Pedido_Producto::ObtenerPorId($data["id"]);

            $fotoPath = Picture::GuardarImagen($auxPP->_fechaIngreso."-".Pedido_Producto::ObtenerNombreCliente($auxPP->_idCliente), "_FotosPedidos");

            $arr = array("_fotoCliente" => $fotoPath);

            Pedido_Producto::Modificar($data["id"], $arr);
            $payload = json_encode(array("mensaje" => "Pedido borrado con exito"));   
            $response->getBody()->write($payload);

            return $response->withHeader('Content-Type', 'application/json');

        }
    }
?>