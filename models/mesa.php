<?php
    include_once './db/AccesoDatos.php';

    class Mesa{
        
        public $_id;
        public $_estado; //"con cliente esperando pedido", "con cliente comiendo", "con cliente pagando", "cerrada"
        public $_codMesa;

        public function CrearMesa(){
            $dbManager = AccesoDatos::obtenerInstancia();
            $query = $dbManager->prepararConsulta("INSERT INTO mesas (_estado, _codMesa) VALUES (:estado, :codMesa)");
            $query->bindValue(':estado', $this->_estado, PDO::PARAM_INT);
            $query->bindValue(':codMesa', $this->_codMesa, PDO::PARAM_STR);
            $query->execute();
            return $dbManager->obtenerUltimoId();   
        }
        public static function ObtenerMesa($codMesa){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDatos->prepararConsulta("SELECT * FROM mesas WHERE _codMesa = :codMesa");
            $query->bindValue(':codMesa', $codMesa, PDO::PARAM_STR);
            $query->execute();

            return $query->fetchObject('Mesa');
        }
        public static function ObtenerMesaLibre(){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDatos->prepararConsulta("SELECT * FROM mesas WHERE _estado = 0");
            $query->execute();
            $mesasLibres = $query->fetchAll(PDO::FETCH_CLASS, 'Mesa'); 

            //Modifico mesa a ocupada
            $mesa = $mesasLibres[rand(0, count($mesasLibres)-1)];
            Mesa::ModificarMesa($mesa->_id, $mesa->_estado+1);

            return $mesa->_codMesa;
        }

        public static function ObtenerTodos(){
            $dbManager = AccesoDatos::obtenerInstancia();
            $query = $dbManager->prepararConsulta("SELECT * FROM mesas");
            $query->execute();

            return $query->fetchAll(PDO::FETCH_CLASS, 'Mesa');
        }
        public static function ModificarMesa($id, $estado){       
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDato->prepararConsulta("UPDATE mesas SET _estado = :estado WHERE _id = :id");
            $query->bindValue(':id', $id, PDO::PARAM_STR);
            $query->bindValue(':estado', $estado);
            $query->execute();
        }
        public static function BorrarMesa($id){
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDato->prepararConsulta("DELETE from mesas WHERE _id = :id");
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();
        } 


    }

?>