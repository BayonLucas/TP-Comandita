<?php
    include_once "AccesoDatos.php";

    class Mesa{
        
        public $_id;
        public $_estado;
        public $_cgoMesa;
    

        public static function getEstado($estado){
            $ret;
            if(is_numeric($estado)){
                switch($estado){
                    case 0:
                        $ret = "con cliente esperando pedido";
                        break;
                    case 1:
                        $ret = "con cliente comiendo";
                        break;
                    case 2:
                        $ret = "con cliente pagando";
                        break;
                    case 3:
                        $ret = "cerrada";
                        break;
                }
            }
            else{
                switch($estado){
                    case "con cliente esperando pedido":
                        $ret = 0;
                        break;
                    case "con cliente comiendo":
                        $ret = 1;
                        break;
                    case "con cliente pagando":
                        $ret = 2;
                        break;
                    case "cerrada":
                        $ret = 3;
                        break;
                }
            }
            return $ret;
        }

        public function CrearMesa(){
           $ret = null;
           try{
                $dbManager = AccesoDatos::obtenerInstancia();
                $query = $dbManager->prepararConsulta("INSERT INTO mesas (estado, cgoMesa) VALUES (:estado, :cgoMesa)");
                $query->bindValue(':estado', Mesa::getEstado($this->_estado));
                $query->bindValue(':cgoMesa', $this->_cgoMesa, PDO::PARAM_STR);
                $query->execute();
                $ret = $dbManager->obtenerUltimoId();
            }
            catch(Throwable $mensaje){
                printf("Error al conectar en la base de datos: <br> $mensaje .<br>");
            }
            finally{
                return $ret;
            }   
        }
    
        public static function ObtenerTodos(){
            $dbManager = AccesoDatos::obtenerInstancia();
            $query = $dbManager->prepararConsulta("SELECT * FROM mesas");
            $query->execute();

            return $query->fetchAll(PDO::FETCH_CLASS, 'Mesa');
        }

        public static function ModificarMesa($id, $estado){       
            try{
                $objAccesoDato = AccesoDatos::obtenerInstancia();
                $query = $objAccesoDato->prepararConsulta("UPDATE mesas SET estado = :estado, WHERE id = :id");
                $query->bindValue(':id', $id, PDO::PARAM_STR);
                $query->bindValue(':estado', Mesa::getEstado($estado));
                $query->execute();
            }
            catch(Throwable $mensaje){
                printf("Error al conectar en la base de datos: <br> $mensaje .<br>");
            }
        }

        public static function BorrarMesa($id){
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDato->prepararConsulta("DELETE from mesas WHERE id = :id");
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();
        } 


    }

?>