<?php    
    include_once './db/AccesoDatos.php';

    class Producto{

        public $_id;
        public $_idSector;
        public $_nombre;
        public $_precio;
        public $_tiempoPreparado;

        public function CrearProducto(){
            $dbManager = AccesoDatos::obtenerInstancia();
            $query = $dbManager->prepararConsulta("INSERT INTO productos (_idSector, _nombre, _precio, _tiempoPreparado) VALUES (:idSector, :nombre, :precio, :tiempoPreparado)");
            $query->bindValue(':idSector', $this->_idSector, PDO::PARAM_INT);
            $query->bindValue(':nombre', $this->_nombre, PDO::PARAM_STR);
            $query->bindValue(':precio', $this->_precio, PDO::PARAM_INT);
            $query->bindValue(':tiempoPreparado', $this->_tiempoPreparado, PDO::PARAM_INT);
            
            $query->execute();
            
            return $dbManager->obtenerUltimoId();
        }
    
        public static function ObtenerTodos(){
            $dbManager = AccesoDatos::obtenerInstancia();
            $query = $dbManager->prepararConsulta("SELECT * FROM productos");
            $query->execute();

            return $query->fetchAll(PDO::FETCH_CLASS, 'Producto');
        }

        public static function ObtenerPorSector($idSector){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDatos->prepararConsulta("SELECT * FROM productos WHERE _idSector = :idSector");
            $query->bindValue(':idSector', $idSector, PDO::PARAM_STR);

            $query->execute();

            return $query->fetchAll(PDO::FETCH_CLASS, 'Producto');
        }

        public static function ObtenerProducto($id){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDatos->prepararConsulta("SELECT * FROM productos WHERE _id = :id");
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();

            return $query->fetchObject('Producto');
        }

        public static function ModificarProducto($id, $precio){       
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDato->prepararConsulta("UPDATE productos SET _precio = :precio WHERE _id = :id");
            $query->bindValue(':id', $id, PDO::PARAM_STR);
            $query->bindValue(':precio', $precio, PDO::PARAM_INT);
            $query->execute();
        }

        public static function BorrarProducto($id){
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDato->prepararConsulta("DELETE from productos WHERE _id = :id");
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();
        } 
 

    }


?>
