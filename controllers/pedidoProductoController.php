<?php
    require_once './models/pedido_producto.php';
    require_once './models/pedido.php';
    require_once './models/usuario.php';
    require_once './models/producto.php';
    require_once './models/mesa.php';
    require_once './models/cliente.php';
    require_once './models/encuesta.php';
    require_once './db/picture.php';

    class Pedido_ProductoController{
        public function CargarUno($request, $response, $args){
            try{
                $params = $request->getParsedBody();
                $ppInfo = $params[0];
                date_default_timezone_set('America/Argentina/Buenos_Aires');

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
                    $pedido->_id = $idPP; 
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
                
            }
            catch(Throwable $mensaje){
                $payload = json_encode(array("Error Exception" => $mensaje));
            }
            finally{
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }
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
            $payload = json_encode(array("mensaje" => "Foto tomada con exito"));   
            $response->getBody()->write($payload);

            return $response->withHeader('Content-Type', 'application/json');
        }

        public function VerEstadoPedido($request, $response, $args){
            $idCliente = $args["idCliente"];
            $cliente = Cliente::ObtenerCliente($idCliente);
            if(isset($cliente) && $cliente->_codMesa != null && $cliente->_codPedido != null){
                $pedido = Pedido_Producto::ObtenerPedidoPorCodigos($cliente->_codPedido, $cliente->_codMesa);
                $payload = json_encode(array("Consulta del Cliente por codigos" => "Estado del pedido: $pedido->_estado - Duracion del pedido: $pedido->_tiempoTotalEspera"));
            }
            else{
                $payload = json_encode(array("Consulta del Cliente por codigos" => "El cliente no posee los codigos correspondientes"));
            }
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }




        //Preparar Pedido
        public function PrepararPedidos($request, $response, $args){
            $data = array();
            //Busco todos los pedidos pendientes
            $pendientes = Pedido::ObtenerPedidosPorEstado(0);
            //Los recorro por un foreach
            if(count($pendientes) > 0){
                foreach($pendientes as $pedido){
                    //Averiguo a que sector corresponde
                    $idSector = (Producto::ObtenerProducto($pedido->_idProducto))->_idSector;
                    //Obtengo, si hay disponibles, el user que lo prepare
                    $arrUsers = Usuario::ObtenerPorEstadoPorSector($idSector, 0); //Disponibles
                    if(count($arrUsers) > 0){
                        //Al prepararlo, cambia los estados de los elemento segun corresponda
                        $aux = $arrUsers[0];
                        Usuario::ModificarUsuario($aux->_id, 1);
                        Pedido::ModificarPedido($pedido->_id, $pedido->_idProducto, "estado", 1);
                        Pedido_Producto::Modificar($pedido->_id, array("_estado" => 1));
                        
                        array_push($data, "id Pedido: $pedido->_id - id Producto: $pedido->_idProducto - Sector: $idSector - Preparado por: $aux->_id-$aux->_nombre-$aux->_rol");
                    }
                }
                $payload = json_encode(array("listaPedidosPreparados" => $data));
            }
            else{
                $payload = json_encode(array("listaPedidosPreparados" => "No se encontraron pedidos"));
            }
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

        //Terminar Pedidos
        public function TerminarPedidos($request, $response, $args){
            $data = array();
            //Obtener listaod de pedudis en preparacion para darles fin
            $preparados = Pedido::ObtenerPedidosPorEstado(1);
            //Recorrer el listado
            if(count($preparados)){
                foreach($preparados as $pedido){
                    $idSector = (Producto::ObtenerProducto($pedido->_idProducto))->_idSector;
                    $arrUsers = Usuario::ObtenerPorEstadoPorSector($idSector, 1); //Ocupados
                    //Modificar estados
                    if(count($arrUsers) > 0){
                        Usuario::ModificarUsuario($arrUsers[0]->_id, 0);
                        Pedido::ModificarPedido($pedido->_id, $pedido->_idProducto, "estado", 2);
    
                        array_push($data, "id Pedido: $pedido->_id - id Producto: $pedido->_idProducto - Sector: $idSector");
                    }
                }
                $payload = json_encode(array("listaPedidosTerminados" => $data));
            }
            else{
                $payload = json_encode(array("listaPedidosTerminados" => "Error en la busqueda de preparados"));
            }
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
        
        //Servir Pedidos
        public function ServirPedidos($request, $response, $args){
            $data = array();
            //Obtengo PedidosProductos No completo
            $listaPP = Pedido_Producto::ObtenerTodosNoListos();
            //Recorro lista
            if(count($listaPP) > 0){
                foreach($listaPP as $item){
                    //Busco sus pedidos y constato de que esten listos para servir
                    $completo = true;
                    $listaPedidos = Pedido::ObtenerPorId($item->_id);
                    foreach($listaPedidos as $pedido){
                        if($pedido->_estado != 2){
                            $completo = false;
                        }
                    }
                    if($completo){
                        //Modifico estados
                        Pedido_Producto::Modificar($item->_id, array("_estado" => 2));
                        Mesa::ModificarEstadoPorCodigo($item->_codMesa, 3);
                        $auxListaPedidos = Pedido::ObtenerPorId($item->_id);
                        foreach($auxListaPedidos as $pedido){
                            Pedido::ModificarPedido($pedido->_id, $pedido->_idProducto, "estado", 3);
                        }
    
                        array_push($data, "id PedidoProducto: $item->_id - id Cliente: $item->_idCliente - codPedido: $item->_codPedido - codMesa: $item->_codMesa");
                    }
                }
                $payload = json_encode(array("listaPedidosTerminados" => $data));
            }
            else{
                $payload = json_encode(array("listaPedidosTerminados" => "Error"));
            }
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
        
        //Cobrar
        public function Cobrar($request, $response, $args){
            $data = array();
            $params = $request->getParsedBody();

            $listaPP = Pedido_Producto::ObtenerTodosLosListos();
            if(count($listaPP) > 0){
                foreach($listaPP as $item){
                    Mesa::ModificarEstadoPorCodigo($item->_codMesa, 4);
                    array_push($data, "id PedidoProducto: $item->_id - id Cliente: $item->_idCliente - codPedido: $item->_codPedido - codMesa: $item->_codMesa - Importe a pagar: $item->_importeTotal");
                }
                $payload = json_encode(array("listaPedidosTerminados" => $data));
            }
            else{
                $payload = json_encode(array("listaPedidosTerminados" => "No se encontraron pedidos Listos para cobrar"));
            }
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

        //Cerrar Mesa
        public function CerrarMesas($request, $response, $args){
            $data = array();
            $listaPP = Pedido_Producto::ObtenerTodosLosListos();
            if(count($listaPP)){
                foreach($listaPP as $item){
                    $mesa = Mesa::ObtenerMesa($item->_codMesa); 
                    if($mesa->_estado == 4){
                        Mesa::ModificarEstadoPorCodigo($item->_codMesa, 5);
                        $fecha = date("Y-m-d H:i:s");
                        Pedido_Producto::Modificar($item->_id, array("_fechaFinalizado" => $fecha));
                        array_push($data, "id PedidoProducto: $item->_id - id Cliente: $item->_idCliente - codPedido: $item->_codPedido - codMesa: $item->_codMesa - Importe Cobrado: $item->_importeTotal - Fecha de Cierre: $fecha");
                    }
                }
            }
            else{
                array_push($data, "No hay pedidos cobrados como para cerrar mesas");
            }
            $payload = json_encode(array("listaMesasCerradas:" => $data));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

        //Encuestar
        public function Encuestar($request, $response, $args){
            $id = $args['id'];
            $params = $request->getParsedBody();
    
            $cliente = Cliente::ObtenerCliente($id);
            if($cliente->_estado == 1){
                $pedido = Pedido_Producto::ObtenerPedidoPorCodigos($cliente->_codPedido, $cliente->_codMesa);
                if($pedido->_estado == 2){
                    //Aplicar Encuesta
                    $encuesta = new Encuesta();
                    $encuesta->_idCliente = $pedido->_idCliente;
                    $encuesta->_idUsuario = $pedido->_idUsuario;
                    $encuesta->_idPedido = $pedido->_id;
                    $encuesta->_idMesa = $pedido->_idMesa;
                    $encuesta->_ptoMesa = $params["ptoMesa"];
                    $encuesta->_ptoMozo = $params["ptoMozo"];
                    $encuesta->_ptoResto = $params["ptoResto"];
                    $encuesta->_ptoChef = $params["ptoChef"];
                    $encuesta->_resenia = $params["resenia"];
                    $encuesta->_fecha = $pedido->_fechaFinalizado;
                    $encuesta->CrearEncuesta();
                    $payload = json_encode(array("Encuesta:" => "Cargada"));
                }
                else{
                    $payload = json_encode(array("Encuesta:" => "El pedido no se ha concretado"));
                }
            }
            else{
                $payload = json_encode(array("Encuesta:" => "El cliente no ha sido atendido"));
            }
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }
?>