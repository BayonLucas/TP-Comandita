<?php
    include_once "AccesoDatos.php";

    class Pedido{

        public $_id;
        public $_idProducto;
        public $_estado; //"Pendiente", "En preparacion", "Listo para servir"
        public $_fechaInicio;
        public $_fechaEstimadaFinal;

        public function CrearPedido(){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (idProducto, estado, fechaInicio, fechaEstimadaFinal) VALUES (:idProducto, :estado, :fechaInicio, :fechaEstimadaFinal)");
            $query->bindValue(':idProducto', $this->_idProducto, PDO::PARAM_INT);
            $query->bindValue(':estado', $this->_estado, PDO::PARAM_INT);
            $query->bindValue(':fechaInicio', $this->_fechaInicio);
            $query->bindValue(':fechaIngreso', $this->_fechaIngreso);
            $query->bindValue(':fechaEstimadaFinal', $this->_fechaEstimadaFinal);

            $query->execute();

            return $objAccesoDatos->obtenerUltimoId();
        }

        public static function ObtenerTodos(){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos");
            $query->execute();

            return $query->fetchAll(PDO::FETCH_CLASS, 'Pedido');
        }

        public static function ObtenerPedido($id){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE id = :id");
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();

            return $query->fetchObject('Pedido');
        }

        // public static function Modificar($id, $estado){
        //     $objAccesoDato = AccesoDatos::obtenerInstancia();
        //     $query = $objAccesoDato->prepararConsulta("UPDATE usuarios SET estado = :estado WHERE id = :id");
        //     $query->bindValue(':estado', $estado, PDO::PARAM_STR);
        //     $query->bindValue(':id', $id, PDO::PARAM_INT);
        //     $query->execute();
        // }

        // public static function BorrarPedido($id){
        //     $objAccesoDato = AccesoDatos::obtenerInstancia();
        //     $query = $objAccesoDato->prepararConsulta("UPDATE pedidos SET fechaAnulado = :fechaAnulado WHERE id = :id");
        //     $fecha = new DateTime(date("d-m-Y"));
        //     $query->bindValue(':id', $id, PDO::PARAM_INT);
        //     $query->bindValue(':fechaAnulado', date_format($fecha, 'Y-m-d H:i:s'));
        //     $query->execute();
        // } 

        public static function BorrarCliente($id){
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDato->prepararConsulta("DELETE from pedidos WHERE id = :id");
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();
        } 


    }




?>
