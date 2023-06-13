<?php
    include_once "AccesoDatos.php";

    class Pedido{

        public $_id;
        public $_idUsuario;
        public $_idCliente;
        public $_idMesa;
        
        public $_productos;
        public $_totalImporte;
        public $_tiempoTotalEstimado;

        public $_cgoPedido;
        public $_fechaIngreso;
        public $_fechaAnulado;

        public function getTotalImporte(){
            $ret = 0;
            foreach($this->_productos as $item){
                $ret += $item->_precio;
            }
            return $ret;
        }
        public function getTiempoTotalEstimado(){
            $ret = 0;
            foreach($this->_productos as $item){
                $ret += $item->_tiempoPreparado;
            }
            return $ret;
        }


        public function CrearPedido(){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (idUsuario, idCliente, idMesa, productos, totalImporte, tiempoTotalEstimado, cgoPedido, fechaIngreso, fechaAnulado) VALUES (:idUsuario, :idCliente, :idMesa, :productos, :totalImporte, :tiempoTotalEstimado, :cgoPedido, :fechaIngreso, :fechaAnulado)");
            $consulta->bindValue(':idUsuario', $this->_idUsuario, PDO::PARAM_INT);
            $consulta->bindValue(':idCliente', $this->_idCliente, PDO::PARAM_INT);
            $consulta->bindValue(':idMesa', $this->_idMesa, PDO::PARAM_INT);
            
            $consulta->bindValue(':productos', json_encode($this->_productos), PDO::PARAM_STR);
            $consulta->bindValue(':totalImporte', $this->_totalImporte, PDO::PARAM_INT);
            $consulta->bindValue(':tiempoTotalEstimado', $this->_tiempoTotalEstimado);
            
            $consulta->bindValue(':cgoPedido', $this->_cgoPedido, PDO::PARAM_STR);
            $consulta->bindValue(':fechaIngreso', $this->_fechaIngreso);
            $consulta->bindValue(':fechaAnulado', $this->_fechaAnulado);

            $consulta->execute();

            return $objAccesoDatos->obtenerUltimoId();
        }

        public static function ObtenerTodos(){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos");
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
        }

        public static function ObtenerPedido($id){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE id = :id");
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
            $consulta->execute();

            return $consulta->fetchObject('Pedido');
        }

        // public static function Modificar($id, $estado){
        //     $objAccesoDato = AccesoDatos::obtenerInstancia();
        //     $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET estado = :estado WHERE id = :id");
        //     $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        //     $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        //     $consulta->execute();
        // }

        public static function BorrarPedido($id){
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET fechaAnulado = :fechaAnulado WHERE id = :id");
            $fecha = new DateTime(date("d-m-Y"));
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
            $consulta->bindValue(':fechaAnulado', date_format($fecha, 'Y-m-d H:i:s'));
            $consulta->execute();
        } 




    }




?>
