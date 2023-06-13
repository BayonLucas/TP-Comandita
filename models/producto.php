<?php    
    include_once "AccesoDatos.php";

    class Producto{

        public $_id;
        public $_nombre;
        public $_precio;
        public $_tiempoPreparado;
        //public $_idSector;

        public function CrearProducto(){
            $ret = null;
            try{
                $dbManager = AccesoDatos::obtenerInstancia();
                $query = $dbManager->prepararConsulta("INSERT INTO productos (nombre, precio, tiempoPreparado) VALUES (:nombre, :precio, :tiempoPreparado)");
                $query->bindValue(':nombre', $this->_nombre, PDO::PARAM_STR);
                $query->bindValue(':precio', $this->_precio, PDO::PARAM_INT);
                $query->bindValue(':tiempoPreparado', $this->_tiempoPreparado, PDO::PARAM_INT);
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
            $query = $dbManager->prepararConsulta("SELECT * FROM productos");
            $query->execute();

            return $query->fetchAll(PDO::FETCH_CLASS, 'Producto');
        }

        public static function ModificarMesa($id, $precio){       
            try{
                $objAccesoDato = AccesoDatos::obtenerInstancia();
                $query = $objAccesoDato->prepararConsulta("UPDATE productos SET precio = :precio, WHERE id = :id");
                $query->bindValue(':id', $id, PDO::PARAM_STR);
                $query->bindValue(':precio', $precio, PDO::PARAM_INT);
                $query->execute();
            }
            catch(Throwable $mensaje){
                printf("Error al conectar en la base de datos: <br> $mensaje .<br>");
            }
        }

        public static function BorrarProducto($id){
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDato->prepararConsulta("DELETE from productos WHERE id = :id");
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();
        } 
 

    }


?>
