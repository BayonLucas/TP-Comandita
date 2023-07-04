<?php
    include_once './db/AccesoDatos.php';

    class Cliente{
        
        public $_id;
        public $_nombre;
        public $_estado; //0 = desatendido - 1 = atendido
        public $_fechaIngreso;
        public $_codMesa;
        public $_codPedido;
        
        public function CrearCliente(){
            $dbManager = AccesoDatos::obtenerInstancia();
            $query = $dbManager->prepararConsulta("INSERT INTO clientes (_nombre, _estado, _fechaIngreso, _codMesa, _codPedido) VALUES (:nombre, :estado, :fechaIngreso, :codMesa, :codPedido)");
            
            $query->bindValue(':nombre', $this->_nombre, PDO::PARAM_STR);
            $query->bindValue(':estado', $this->_estado, PDO::PARAM_INT);
            $query->bindValue(':fechaIngreso', date_format(new DateTime($this->_fechaIngreso), 'Y-m-d H:i:s'));
            $query->bindValue(':codMesa', $this->_codMesa, PDO::PARAM_STR);
            $query->bindValue(':codPedido', $this->_codPedido, PDO::PARAM_STR);
            $query->execute();

            return $dbManager->obtenerUltimoId();   
        }
    
        public static function ObtenerTodos(){
            $dbManager = AccesoDatos::obtenerInstancia();
            $query = $dbManager->prepararConsulta("SELECT * FROM clientes");
            $query->execute();

            return $query->fetchAll(PDO::FETCH_CLASS, 'Cliente');
        }

        public static function ObtenerCliente($id){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDatos->prepararConsulta("SELECT * FROM clientes WHERE _id = :id");
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();

            return $query->fetchObject('Cliente');
        }

        public static function ModificarCliente($id, $estado){       
            try{
                $objAccesoDato = AccesoDatos::obtenerInstancia();
                $query = $objAccesoDato->prepararConsulta("UPDATE clientes SET _estado = :estado WHERE _id = :id");
                $query->bindValue(':id', $id, PDO::PARAM_INT);
                $query->bindValue(':estado', $estado, PDO::PARAM_INT);
                $query->execute();
            }
            catch(Throwable $mensaje){
                printf("Error al conectar en la base de datos: <br> $mensaje .<br>");
            }
        }

        public static function BorrarCliente($id){
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDato->prepararConsulta("DELETE from clientes WHERE _id = :id");
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();
        }
        
        public static function RecibirCodigoPedido($id, $cod){
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDato->prepararConsulta("UPDATE clientes SET _codPedido = :codPedido WHERE _id = :id");
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->bindValue(':codPedido', $cod, PDO::PARAM_STR);
            $query->execute();
        }

    }
?>