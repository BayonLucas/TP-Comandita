<?php
    require_once '.\db\AccesoDatos.php';

    class CsvController{
        public static function DescargarCSV($request, $response, $args){
            $entidad = $args["entidad"];
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDatos->prepararConsulta("SELECT * FROM $entidad" );
            $query->execute();
            $lista = $query->fetchAll(PDO::FETCH_ASSOC);
            $csv = '';
            if(count($lista) > 0){
                $header = array_keys($lista[0]);
                $csv .= implode(',', $header) . "\n";
                foreach ($lista as $row) {
                    $csv .= implode(',', $row) . "\n";
                }
            }
            else{
                $csv .= "Error: Entidad inexistente";
            }
            $response = $response->withHeader('Content-Type', 'text/csv')
                            ->withHeader('Content-Disposition', 'attachment; filename="' . $entidad.'.csv"')
                            ->withBody(new \Slim\Psr7\Stream(fopen('php://temp', 'r+')));
            $response->getBody()->write($csv);
            return $response;
        }

        public static function Cargarcsv($request, $response, $args){
            $entidad = $args['entidad'];
            $uploadedFile = $request->getUploadedFiles()['csv'];
            $csvFile = $uploadedFile->getStream()->getMetadata('uri');
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
    
            // Leer el contenido del archivo CSV
            $csvContent = file($csvFile);
            $keys = str_getcsv(array_shift($csvContent));
            $content = array_map('str_getcsv', $csvContent);
    
            // Verificar si las claves del archivo CSV coinciden con las columnas de la entidad
            $query = $objAccesoDatos->prepararConsulta("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = :entidad");
            $query->bindValue(':entidad', $entidad);
            $query->execute();
            $colEntidad = [];
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $colEntidad[] = $row['COLUMN_NAME'];
            }
            $diferencias = array_diff($keys, $colEntidad);
            if (empty($diferencias)) {
                // Borrar todos los registros de la entidad
                $query = $objAccesoDatos->prepararConsulta("DELETE FROM $entidad");
                $query->execute();
    
                // Cargar los datos del archivo CSV en la entidad
                $colsStr = implode(', ', $keys);
                $values = [];
                foreach ($content as $item) {
                    $values[] = "('" . implode("', '", $item) . "')";
                }
                $valuesStr = implode(', ', $values);
                $query = $objAccesoDatos->prepararConsulta("INSERT INTO $entidad ($colsStr) VALUES $valuesStr");
                $query->execute();
    
                // Devolver una respuesta exitosa
                $response->getBody()->write(' se pudo realizar la carga');
                return $response->withStatus(200);
            } 
            else {
                // Devolver una respuesta de error
                return 500;
            }
        }
        
    }
 

?>