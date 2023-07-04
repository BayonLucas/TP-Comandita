<?php
    require_once './models/pedido_producto.php';
    require_once './models/pedido.php';
    require_once './models/usuario.php';
    require_once './models/producto.php';
    require_once './models/mesa.php';
    require_once './models/cliente.php';
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
            $importeTotal = Pedido_Producto::ObtenerPrecioTotal($idPP);

            //Modifico al pedido_Producto para agregar los datos finales salvo la imagen
            $arr = array("_tiempoTotalEspera" => $tiempoTotalEspera, "_importeTotal" => $importeTotal);
            Pedido_Producto::Modificar($idPP, $arr);

            //Cambio de estados y devolucion de datos
            Mesa::ModificarEstadoPorCodigo($pp->_codMesa, 2);
            Cliente::ModificarCliente($pp->_idCliente, 1);
            Cliente::RecibirCodigoPedido($pp->_idCliente, $pp->_codPedido);

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

            $fotoPath = Picture::GuardarImagen(date_format(new DateTime($auxPP->_fechaIngreso), "Y-m-d")."-".Pedido_Producto::ObtenerNombreCliente($auxPP->_idCliente), "_FotosPedidos");

            $arr = array("_fotoCliente" => $fotoPath);

            Pedido_Producto::Modificar($data["id"], $arr);
            $payload = json_encode(array("mensaje" => "Pedido borrado con exito"));   
            $response->getBody()->write($payload);

            return $response->withHeader('Content-Type', 'application/json');
        }

        //Preparar Pedido
        public function PrepararPedidos($request, $response, $args){
            $data = array();
            //Busco todos los pedidos pendientes
            $pendientes = Pedido::ObtenerPedidosPorEstado(0);
            //Los recorro por un foreach
            foreach($pendientes as $pedido){
                //Averiguo a que sector corresponde
                $idSector = (Producto::ObtenerProducto($pedido->_idProducto))->_idSector;
                //Obtengo, si hay disponibles, el user que lo prepare
                $arrUsers = Usuario::ObtenerPorEstadoPorSector($idSector, 0); //Disponibles
                if(count($arrUsers) != 0){
                    //Al prepararlo, cambia los estados de los elemento segun corresponda
                    Usuario::ModificarUsuario($arrUsers[0]->_id, 1);
                    Pedido::ModificarPedido($pedido->_id, $pedido->_idProducto, "estado", 1);
                    Pedido_Producto::Modificar($pedido->_id, array("_estado" => 1));
                    
                    array_push($data, `id Pedido: $pedido->_id - id Producto: $pedido->_idProducto - Sector: $idSector - Preparado por: {$arrUsers[0]->_id}`);
                }
                else{
                    //Caso contrario, paso al siguiente y vuelvo a preguntar
                    continue;
                }
            }
            $payload = json_encode(array("listaPedidosPreparados" => $data));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

        //Terminar Pedidos
        public function TerminarPedidos($request, $response, $args){
            $data = array();
            //Obtener listaod de pedudis en preparacion para darles fin
            $preparados = Pedido::ObtenerPedidosPorEstado(1);
            //Recorrer el listado
            foreach($preparados as $pedido){
                $idSector = (Producto::ObtenerProducto($pedido->_idProducto))->_idSector;
                $arrUsers = Usuario::ObtenerPorEstadoPorSector($idSector, 1); //Ocupados
                //Modificar estados
                if(count($arrUsers) != 0){
                    Usuario::ModificarUsuario($arrUsers[0]->_id, 0);
                    Pedido::ModificarPedido($pedido->_id, $pedido->_idProducto, "estado", 2);

                    array_push($data, `id Pedido: $pedido->_id - id Producto: $pedido->_idProducto - Sector: $idSector`);
                }
                else{
                    continue;
                }
            }
            $payload = json_encode(array("listaPedidosTerminados" => $data));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
        
        //Servir Pedidos
        public function ServirPedidos($request, $response, $args){
            $data = array();
            //Obtengo PedidosProductos No completo
            $listaPP = Pedido_Producto::ObtenerTodosNoListos();
            //Recorro lista
            foreach($listaPP as $item){
                //Busco sus pedidos y constato de que esten listos para servir
                $completo = true;
                $listaPedidos = Pedido::ObtenerPorId($item->_id);
                foreach($listaPedidos as $pedido){
                    if($pedido->_estado != 2){
                        $completo = false;
                        break;
                    }
                }
                if($completo){
                    //Modifico estados
                    Pedido_Producto::Modificar($item->_id, array("_estado" => 2));
                    Mesa::ModificarEstadoPorCodigo($item->_codMesa, 3);

                    array_push($data, "id PedidoProducto: $item->_id - id Cliente: $item->_idCliente - codPedido: $item->_codPedido - codMesa: $item->_codMesa");
                }
            }

            $payload = json_encode(array("listaPedidosTerminados" => $data));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
        
        //Cobrar
        public function Cobrar($request, $response, $args){
            $data = array();
            $listaPP = Pedido_Producto::ObtenerTodosLosListos();
            foreach($listaPP as $item){
                Mesa::ModificarEstadoPorCodigo($item->_codMesa, 4);
                array_push($data, "id PedidoProducto: $item->_id - id Cliente: $item->_idCliente - codPedido: $item->_codPedido - codMesa: $item->_codMesa - Importe a pagar: $item->_importeTotal");
            }
            $payload = json_encode(array("listaPedidosTerminados" => $data));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

        //Cerrar Mesa
        public function CerrarMesa($request, $response, $args){
            $data = array();
            $listaPP = Pedido_Producto::ObtenerTodosLosListos();
            foreach($listaPP as $item){
                $mesa = Mesa::ObtenerMesa($item->_codMesa); 
                if($mesa->_estado == 4){
                    Mesa::ModificarEstadoPorCodigo($item->_codMesa, 5);
                    Pedido_Producto::Modificar($item->_id, array("_estado" => 2, "_fechaFinalizado" => new DateTime(date("Y-m-d H:i:s"))));
                }
                array_push($data, "id PedidoProducto: $item->_id - id Cliente: $item->_idCliente - codPedido: $item->_codPedido - codMesa: $item->_codMesa - Importe Cobrado: $item->_importeTotal - Fecha de Cierre: $item->_fechaFinalizado");
            }
            $payload = json_encode(array("listaMesasCerradas:" => $data));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

    }
?>