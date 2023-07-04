<?php
    include_once './db/AccesoDatos.php';

    class Encuesta{
        public $_id;
        public $_idCliente;
        public $_idUsuario;
        public $_idPedido;
        public $_idMesa;
        public $_ptoMesa;
        public $_ptoMozo;
        public $_ptoResto;
        public $_ptoChef;
        public $_resenia;
        public $_fecha;

        public function CrearEncuesta(){
            $dbManager = AccesoDatos::obtenerInstancia();
            $query = $dbManager->prepararConsulta("INSERT INTO clientes (_idCliente, _idUsuario, _idPedido, _idMesa, _ptoMesa, _ptoMozo, _ptoResto, _ptoChef, _resenia, _fecha) VALUES (:idCliente, :idUsuario, :idPedido, :idMesa, :ptoMesa, :ptoMozo, :ptoResto, :ptoChef, :resenia, :fecha)");
            
            $query->bindValue(':idCliente', $this->_idCliente, PDO::PARAM_INT);
            $query->bindValue(':idUsuario', $this->_idUsuario, PDO::PARAM_INT);
            $query->bindValue(':idPedido', $this->_idPedido, PDO::PARAM_INT);
            $query->bindValue(':idMesa', $this->_idMesa, PDO::PARAM_INT);
            $query->bindValue(':ptoMesa', $this->_ptoMesa, PDO::PARAM_INT);
            $query->bindValue(':ptoMozo', $this->_ptoMozo, PDO::PARAM_INT);
            $query->bindValue(':ptoResto', $this->_ptoResto, PDO::PARAM_INT);
            $query->bindValue(':ptoChef', $this->_ptoChef, PDO::PARAM_INT);
            $query->bindValue(':resenia', $this->_resenia, PDO::PARAM_STR);
            $query->bindValue(':fecha', $this->_fecha);

            $query->execute();
            return $dbManager->obtenerUltimoId();   
        }


    }
?>