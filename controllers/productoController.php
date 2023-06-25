<?php
require_once './models/producto.php';

    class ProductoController{
        public function CargarUno($request, $response, $args){
            $params = $request->getParsedBody();
            // Creamos el Producto
                $producto = new Producto();
                $producto->_idSector = $params["idSector"];
                $producto->_nombre = $params["nombre"];
                $producto->_precio = $params["precio"];
                $producto->_tiempoPreparado = $params["tiempoPreparado"];                
            $producto->CrearProducto();

            $payload = json_encode(array("mensaje" => "Producto creado con exito"));

            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

        public function TraerUno($request, $response, $args){
            //Buscamos producto por id
            $id = $args['id'];
            $aux = Producto::obtenerProducto($id);
            $payload = json_encode($aux);
            
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

        public function TraerTodos($request, $response, $args){
            $lista = Producto::ObtenerTodos();
            $payload = json_encode(array("listaProductos" => $lista));

            $response->getBody()->write($payload);

            return $response->withHeader('Content-Type', 'application/json');
        }

        public function TraerPorSector($request, $response, $args){
            $sector = $args['idSector'];    
            $lista = Producto::ObtenerPorSector($sector);
            $payload = json_encode(array("listaProductos($sector)" => $lista));

            $response->getBody()->write($payload);

            return $response->withHeader('Content-Type', 'application/json');
        }

        public function ModificarUno($request, $response, $args){
            $data = $request->getParsedBody();
            Producto::modificarProducto($data["id"], $data["precio"]);

            $payload = json_encode(array("mensaje" => "Producto modificado con exito"));

            $response->getBody()->write($payload);
            return $response
              ->withHeader('Content-Type', 'application/json');
        }

        public function BorrarUno($request, $response, $args){
            $data = $request->getParsedBody();
            Producto::borrarProducto($data["id"]);
            $payload = json_encode(array("mensaje" => "Producto borrado con exito"));   
            $response->getBody()->write($payload);

            return $response->withHeader('Content-Type', 'application/json');
        }
    }
?>