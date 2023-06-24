<?php
    include_once "AccesoDatos.php";

    class Mesa{
        
        public $_id;
        public $_estado; //"con cliente esperando pedido", "con cliente comiendo", "con cliente pagando", "cerrada"
        public $_codMesa;

        public function CrearMesa(){
            $dbManager = AccesoDatos::obtenerInstancia();
            $query = $dbManager->prepararConsulta("INSERT INTO mesas (estado, codMesa) VALUES (:estado, :codMesa)");
            $query->bindValue(':estado', $this->_estado, PDO::PARAM_INT);
            $query->bindValue(':codMesa', $this->_codMesa, PDO::PARAM_STR);
            $query->execute();
            return $dbManager->obtenerUltimoId();   
        }
    
        public static function ObtenerTodos(){
            $dbManager = AccesoDatos::obtenerInstancia();
            $query = $dbManager->prepararConsulta("SELECT * FROM mesas");
            $query->execute();

            return $query->fetchAll(PDO::FETCH_CLASS, 'Mesa');
        }

        public static function ModificarMesa($id, $estado){       
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDato->prepararConsulta("UPDATE mesas SET estado = :estado, WHERE id = :id");
            $query->bindValue(':id', $id, PDO::PARAM_STR);
            $query->bindValue(':estado', $estado);
            $query->execute();
        }

        public static function BorrarMesa($id){
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDato->prepararConsulta("DELETE from mesas WHERE id = :id");
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();
        } 


    }

?>