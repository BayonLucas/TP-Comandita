<?php
    include_once "AccesoDatos.php";

    class Sector{
        public $_id;
        public $_nombre;
        // public $_disponibilidad;

        public function __construc($id, $nombre){
            $this->_id = $id;
            $this->_nombre = $nombre;
        }

        public function CrearSector(){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO sectores (nombre) VALUES (:nombre)");
            $consulta->bindValue(':nombre', $this->_nombre, PDO::PARAM_STR);
        
            $consulta->execute();

            return $objAccesoDatos->obtenerUltimoId();
        }

        public static function ObtenerTodos(){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM sectores");
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_CLASS, 'Sector');
        }

        public static function ObtenerSector($id){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM sectores WHERE id = :id");
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
            $consulta->execute();

            return $consulta->fetchObject('Sector');
        }
    }
?>