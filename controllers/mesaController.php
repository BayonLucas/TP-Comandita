<?php
require_once './models/mesa.php';

    class MesaController{
        public function CargarUno($request, $response, $args){
            $params = $request->getParsedBody();
                $mesa = new Mesa();
                $mesa->_estado = $params["estado"];
                $mesa->_codMesa = $params["codMesa"];
                
            $mesa->CrearMesa();

            $payload = json_encode(array("mensaje" => "Mesa creada con exito"));

            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
        public function TraerUno($request, $response, $args){
            $data = $args['codMesa'];
            $mesa = Mesa::obtenerMesa($data);
            $payload = json_encode($mesa);
            
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
        public function TraerTodos($request, $response, $args){
            $lista = Mesa::ObtenerTodos();
            $payload = json_encode(array("listaMesas" => $lista));

            $response->getBody()->write($payload);

            return $response->withHeader('Content-Type', 'application/json');
        }
        public function ModificarUno($request, $response, $args){
            $data = $request->getParsedBody();
            Mesa::modificarMesa($data["id"], $data["estado"]);

            $payload = json_encode(array("mensaje" => "Mesa modificado con exito"));

            $response->getBody()->write($payload);
            return $response
              ->withHeader('Content-Type', 'application/json');
        }
    }
?>