<?php
    include_once './db/AccesoDatos.php';

    class Pedido{

        public $_id;
        public $_idProducto;
        public $_cantidad;
        public $_estado; //"Pendiente", "En preparacion", "Listo para servir"
        public $_fechaInicio;
        public $_fechaEstimadaFinal;

        public function CrearPedido(){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (_id, _idProducto, _cantidad, _estado, _fechaInicio, _fechaEstimadaFinal) VALUES (:id, :idProducto, :cantidad, :estado, :fechaInicio, :fechaEstimadaFinal)");
            $query->bindValue(':id', $this->_id, PDO::PARAM_INT);
            $query->bindValue(':idProducto', $this->_idProducto, PDO::PARAM_INT);
            $query->bindValue(':cantidad', $this->_cantidad, PDO::PARAM_INT);
            $query->bindValue(':estado', $this->_estado, PDO::PARAM_INT);
            $query->bindValue(':fechaInicio', $this->_fechaInicio);
            $query->bindValue(':fechaEstimadaFinal', date_format($this->_fechaEstimadaFinal, "y-m-d H:i:s"));

            $query->execute();

            return $objAccesoDatos->obtenerUltimoId();
        }

        public static function ObtenerTodos(){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos");
            $query->execute();

            return $query->fetchAll(PDO::FETCH_CLASS, 'Pedido');
        }

        public static function ObtenerPedido($id, $idProducto){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE _id = :id AND _idProducto = :idProducto");
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->bindValue(':idProducto', $idProducto, PDO::PARAM_INT);
            $query->execute();

            return $query->fetchObject('Pedido');
        }

        public static function ObtenerPorId($id){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE _id = :id");
            $query->bindValue(':id', $id, PDO::PARAM_STR);

            $query->execute();

            return $query->fetchAll(PDO::FETCH_CLASS, 'Pedido');
        }

        public static function ModificarPedido($id, $idProducto, $cambio, $valor){
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $queryStr = "UPDATE pedidos SET ";
            switch($cambio){ // 0 = cantidad / 1 = estado 
                case "cantidad":
                    $queryStr .= "_cantidad = :cantidad ";
                    break;
                case "estado":
                    $queryStr .= "_estado = :estado ";
                    break;    
            }
            $queryStr .= "WHERE _id = :id AND _idProducto = :idProducto";
            $query = $objAccesoDato->prepararConsulta($queryStr);
            $query->bindValue(":$cambio", $valor, PDO::PARAM_INT);
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->bindValue(':idProducto', $idProducto, PDO::PARAM_INT);
            $query->execute();
        }

        public static function BorrarPedido($id, $idProducto){
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDato->prepararConsulta("UPDATE pedidos SET _cantidad = :cantidad WHERE _id = :id AND _idProducto = :idProducto");
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->bindValue(':idProducto', $idProducto, PDO::PARAM_INT);
            $query->bindValue(':cantidad', 0, PDO::PARAM_INT);
            $query->execute();
        } 

        public static function ObtenerPedidosOrdenadosPorFecha(){ //Provisoria
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE pedidos._estado = 0 ORDER BY pedidos._fechaInicio");
            $query->execute();

            return $query->fetchAll(PDO::FETCH_CLASS, 'Pedido');
        }

        public static function ObtenerPedidosPorSector($idSector){ //Provisoria
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDatos->prepararConsulta("SELECT pedidos._id, pedidos._idProducto, pedidos._cantidad, pedidos._estado, pedidos._fechaInicio, pedidos._fechaEstimadaFinal FROM pedidos INNER JOIN productos on pedidos._idProducto = productos._id WHERE productos._idSector = :idSector AND pedidos._estado = 0 ORDER by pedidos._fechaInicio");
            $query->bindValue(':idSector', $idSector, PDO::PARAM_INT);
            $query->execute();

            return $query->fetchAll(PDO::FETCH_CLASS, 'Pedido');
        }

        public static function ObtenerPedidosPorEstado($estado){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE pedidos._estado = :estado ORDER BY pedidos._fechaInicio");
            $query->bindValue(':estado', $estado, PDO::PARAM_INT);

            $query->execute();

            return $query->fetchAll(PDO::FETCH_CLASS, 'Pedido');
        }

        
    }




?>
