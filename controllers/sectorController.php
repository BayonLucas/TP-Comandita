<?php
require_once './models/sector.php';

    class SectorController{
        public function CargarUno($request, $response, $args){
            $params = $request->getParsedBody();
            // Creamos el Sector
                $sector = new Sector();
                $sector->_nombre = $params["nombre"];
                
            $sector->CrearSector();

            $payload = json_encode(array("mensaje" => "Sector creado con exito"));

            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

        public function TraerUno($request, $response, $args){
            //Buscamos Sectpr por id
            $data = $args['id'];
            $sector = Sector::obtenerSector($data);
            $payload = json_encode($sector);
            
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

        public function TraerTodos($request, $response, $args){
            $lista = Sector::ObtenerTodos();
            $payload = json_encode(array("listaSectores" => $lista));

            $response->getBody()->write($payload);

            return $response->withHeader('Content-Type', 'application/json');
        }
    }
?>