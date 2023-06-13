<?php
    include_once "AccesoDatos.php";

    class Cliente{
        
        public $_id;
        public $_nombre;
        public $_estado;
        public $_fechaIngreso;
        public $_cgoMesa;
        
        public function __construct($nombre){
            $this->nombre = $nombre;
            $this->_estado = 0; //0 = desatendido - 1 = atendido
            $this->_fechaIngreso = date("Y-m-a");
            $this->_cgoMesa = null;
        }
    
        public function CrearCliente(){
           $ret = null;
           try{
                $dbManager = AccesoDatos::obtenerInstancia();
                $query = $dbManager->prepararConsulta("INSERT INTO clientes (nombre, estado, fechaIngreso, cgoMesa) VALUES (:nombre, :estado, :fechaRegistro, :cgoMesa)");
                $query->bindValue(':nombre', $this->_nombre, PDO::PARAM_STR);
                if($this->_estado == 0){
                    $query->bindValue(':estado', "Desatendido", PDO::PARAM_STR);
                }
                else{
                    $query->bindValue(':estado', "Atendido", PDO::PARAM_STR);
                }
                $query->bindValue(':fechaIngreso', date_format($this->_fechaIngreso, 'Y-m-d H:i:s'));
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
            $query = $dbManager->prepararConsulta("SELECT * FROM clientes");
            $query->execute();

            return $query->fetchAll(PDO::FETCH_CLASS, 'Cliente');
        }

        public static function ModificarCliente($id, $estado){       
            try{
                $objAccesoDato = AccesoDatos::obtenerInstancia();
                $query = $objAccesoDato->prepararConsulta("UPDATE cliente SET estado = :estado, WHERE id = :id");
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