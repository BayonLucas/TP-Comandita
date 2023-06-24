<?php
    include_once "AccesoDatos.php";

    class Pedido_Producto{

        public $_id;
        public $_idUsuario;
        public $_idCliente;
        public $_idMesa;
        public $_idPedido;

        public $_codPedido;
        public $_codMesa;
        public $estado; //0 - Pendiente // 1 - En preparación // 2 - Listo 

        public $_fechaIngreso;
        public $_tiempoTotalEspera;
        public $_fechaFinalizado;
        public $_importeTotal;

        public $_fotoCliente;

        public $_fechaAnulado;


        public function CrearPedido_Producto(){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDatos->prepararConsulta("INSERT INTO pedido_producto (idUsuario, idCliente, idMesa, idPedido, codPedido, codMesa, estado, fechaIngreso, tiempoTotalEspera, fechaFinalizado, importeTotal, fotoCliente, fechaAnulado) VALUES (:idUsuario, :idCliente, :idMesa, :idPedido, :codPedido, :codMesa, estado, :fechaIngreso, :tiempoTotalEspera, :fechaFinalizado, :importeTotal, :fotoCliente, :fechaAnulado)");
            $query->bindValue(':idUsuario', $this->_idUsuario, PDO::PARAM_INT);
            $query->bindValue(':idCliente', $this->_idCliente, PDO::PARAM_INT);
            $query->bindValue(':idMesa', $this->_idMesa, PDO::PARAM_INT);
            $query->bindValue(':idPedido', $this->_idPedido, PDO::PARAM_INT);
            
            $query->bindValue(':codPedido', $this->_codPedido, PDO::PARAM_STR);
            $query->bindValue(':codMesa', $this->_codMesa, PDO::PARAM_STR);
            $query->bindValue(':estado', $this->_estado, PDO::PARAM_STR);
            
            $query->bindValue(':fechaIngreso', $this->_fechaIngreso);
            $query->bindValue(':tiempoTotalEspera', $this->_tiempoTotalEspera);
            $query->bindValue(':fechaFinalizado', $this->_fechaFinalizado);
            $query->bindValue(':totalImporte', $this->_totalImporte, PDO::PARAM_INT);
            
            $query->bindValue(':fotoCliente', $this->_fotoCliente, PDO::PARAM_STR);

            $query->bindValue(':fechaAnulado', $this->_fechaAnulado);

            $query->execute();

            return $objAccesoDatos->obtenerUltimoId();
        }

        public static function ObtenerTodos(){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDatos->prepararConsulta("SELECT * FROM pedido_producto");
            $query->execute();

            return $query->fetchAll(PDO::FETCH_CLASS, 'Pedido');
        }

        public static function ObtenerPedido($id){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDatos->prepararConsulta("SELECT * FROM pedido_producto WHERE id = :id");
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

        public static function BorrarPedido($id){
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDato->prepararConsulta("UPDATE pedido_producto SET fechaAnulado = :fechaAnulado WHERE id = :id");
            $fecha = new DateTime(date("d-m-Y"));
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->bindValue(':fechaAnulado', date_format($fecha, 'Y-m-d H:i:s'));
            $query->execute();
        } 




    }



?>