<?php
    include_once "AccesoDatos.php";

    class Cliente{
        
        public $_id;
        public $_nombre;
        public $_estado; //0 = desatendido - 1 = atendido
        public $_fechaIngreso;
        public $_cgoMesa;
        public $_cgoPedido;
        
        public function __construct($nombre){
            $this->nombre = $nombre;
            $this->_estado = 0; 
            $this->_fechaIngreso = date("Y-m-a");
            $this->_cgoMesa = null;
            $this->_cgoPedido = null;
        }
    
        public function CrearCliente(){
            $dbManager = AccesoDatos::obtenerInstancia();
            $query = $dbManager->prepararConsulta("INSERT INTO clientes (nombre, estado, fechaIngreso, cgoMesa, cgoPedido) VALUES (:nombre, :estado, :fechaRegistro, :cgoMesa, :cgoPedido)");
            $query->bindValue(':nombre', $this->_nombre, PDO::PARAM_STR);
            $query->bindValue(':estado', $this->_estado, PDO::PARAM_INT);
            $query->bindValue(':fechaIngreso', date_format($this->_fechaIngreso, 'Y-m-d H:i:s'));
            $query->bindValue(':cgoMesa', $this->_cgoMesa, PDO::PARAM_STR);
            $query->bindValue(':cgoPedido', $this->_cgoPedido, PDO::PARAM_STR);
            $query->execute();

            return $dbManager->obtenerUltimoId();   
        }
    
        public static function ObtenerTodos(){
            $dbManager = AccesoDatos::obtenerInstancia();
            $query = $dbManager->prepararConsulta("SELECT * FROM clientes");
            $query->execute();

            return $query->fetchAll(PDO::FETCH_CLASS, 'Cliente');
        }

        public static function ModificarCliente($id, $estado){       
            try{
                $objAccesoDato = AccesoDatos::obtenerInstancia();
                $query = $objAccesoDato->prepararConsulta("UPDATE clientes SET estado = :estado, WHERE id = :id");
                $query->bindValue(':id', $id, PDO::PARAM_STR);
                $query->bindValue(':estado', $estado, PDO::PARAM_STR);
                $query->execute();
            }
            catch(Throwable $mensaje){
                printf("Error al conectar en la base de datos: <br> $mensaje .<br>");
            }
        }

        public static function BorrarCliente($id){
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDato->prepararConsulta("DELETE from clientes WHERE id = :id");
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();
        } 
    }
?>