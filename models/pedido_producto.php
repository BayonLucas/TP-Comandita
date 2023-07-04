<?php
    include_once './db/AccesoDatos.php';
    require_once './models/cliente.php';

    class Pedido_Producto{

        public $_id;
        public $_idUsuario;
        public $_idCliente;
        public $_idMesa;
        //public $_idPedido;

        public $_codPedido;
        public $_codMesa;
        public $_estado; //0 - Pendiente // 1 - En preparación // 2 - Listo 

        public $_fechaIngreso;
        public $_tiempoTotalEspera;
        public $_fechaFinalizado;
        public $_importeTotal;

        public $_fotoCliente;

        public $_fechaAnulado;


        public function CrearPedido_Producto(){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDatos->prepararConsulta("INSERT INTO pedido_producto (_idUsuario, _idCliente, _idMesa,/* idPedido,*/ _codPedido, _codMesa, _estado, _fechaIngreso, _tiempoTotalEspera, _fechaFinalizado, _importeTotal, _fotoCliente, _fechaAnulado) VALUES (:idUsuario, :idCliente, :idMesa,/* :idPedido,*/ :codPedido, :codMesa, :estado, :fechaIngreso, :tiempoTotalEspera, :fechaFinalizado, :importeTotal, :fotoCliente, :fechaAnulado)");
            $query->bindValue(':idUsuario', $this->_idUsuario, PDO::PARAM_INT);
            $query->bindValue(':idCliente', $this->_idCliente, PDO::PARAM_INT);
            $query->bindValue(':idMesa', $this->_idMesa, PDO::PARAM_INT);
            //$query->bindValue(':idPedido', $this->_idPedido, PDO::PARAM_INT);
            
            $query->bindValue(':codPedido', $this->_codPedido, PDO::PARAM_STR);
            $query->bindValue(':codMesa', $this->_codMesa, PDO::PARAM_STR);
            $query->bindValue(':estado', $this->_estado, PDO::PARAM_STR);
            
            $query->bindValue(':fechaIngreso', $this->_fechaIngreso);
            $query->bindValue(':tiempoTotalEspera', $this->_tiempoTotalEspera);
            $query->bindValue(':fechaFinalizado', $this->_fechaFinalizado);
            $query->bindValue(':importeTotal', $this->_importeTotal, PDO::PARAM_INT);
            
            $query->bindValue(':fotoCliente', $this->_fotoCliente, PDO::PARAM_STR);

            $query->bindValue(':fechaAnulado', $this->_fechaAnulado);

            $query->execute();

            return $objAccesoDatos->obtenerUltimoId();
        }
        public static function ObtenerTodos(){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDatos->prepararConsulta("SELECT * FROM pedido_producto");
            $query->execute();

            return $query->fetchAll(PDO::FETCH_CLASS, 'Pedido_Producto');
        }
        public static function ObtenerPorId($id){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDatos->prepararConsulta("SELECT * FROM pedido_producto WHERE _id = :id");
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();

            return $query->fetchObject('Pedido_Producto');
        }
        public static function Modificar($id, $arrAsoc){
            $set = '';
            foreach ($arrAsoc as $key => $value) {
                $set .= "$key = :$key, ";
            }
            $set = rtrim($set, ', ');
            
            $objAccesoDato = AccesoDatos::obtenerInstancia();            
            $sql = "UPDATE pedido_producto SET $set WHERE _id = :id";

            $query = $objAccesoDato->prepararConsulta($sql);
            foreach ($arrAsoc as $key => $value) {
                $query->bindValue(":$key", $value);
            }
            $query->bindValue(':id', $id);
            $query->execute();
        }
        public static function BorrarPedidoProducto($id){
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDato->prepararConsulta("UPDATE pedido_producto SET _fechaAnulado = :fechaAnulado WHERE _id = :id");
            $fecha = new DateTime(date("Y-m-d H:i:s"));
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->bindValue(':fechaAnulado', date_format($fecha, 'Y-m-d H:i:s'));
            $query->execute();
        } 
        public static function ObtenerCodPedidoNuevo($listaPedidosExistentes){
            $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $input_length = strlen($permitted_chars);
            $exist = true;
        
            while($exist){
                $random_string = '';
                for($i = 0; $i < 5; $i++){
                    $random_character = $permitted_chars[mt_rand(0, $input_length - 1)];
                    $random_string .= $random_character;
                }   
        
                $exist = false;
                foreach($listaPedidosExistentes as $item){
                    if($item->_codPedido == $random_string){
                        $exist = true;
                        break;
                    }
                }
            }
            return $random_string;
        }
        public static function ObtenerCodMesa($idCliente){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDatos->prepararConsulta("SELECT _codMesa FROM clientes WHERE _id = :id");
            $query->bindValue(':id', $idCliente, PDO::PARAM_INT);
            $query->execute();

            return ($query->fetchObject('Cliente'))->_codMesa;
        }
        public static function ObtenerNombreCliente($idCliente){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDatos->prepararConsulta("SELECT _nombre FROM clientes WHERE _id = :id");
            $query->bindValue(':id', $idCliente, PDO::PARAM_INT);
            $query->execute();

            return ($query->fetchObject('Cliente'))->_nombre;
        }
        public static function ObtenerPrecioTotal($idPP){      
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDatos->prepararConsulta("SELECT SUM(productos._precio * pedidos._cantidad) as 'Total' from productos INNER JOIN pedidos on pedidos._id = :id WHERE pedidos._idProducto = productos._id");
            $query->bindValue(':id', $idPP, PDO::PARAM_INT);
            $query->execute();

            $row = $query->fetch(PDO::FETCH_ASSOC);
            return $row['Total'];
        }
        public static function ObtenerIdMesa($idCliente){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDatos->prepararConsulta("SELECT mesas._id FROM mesas INNER JOIN clientes ON clientes._codMesa = mesas._codMesa WHERE clientes._id = :id");
            $query->bindValue(':id', $idCliente, PDO::PARAM_INT);
            $query->execute();

            $row = $query->fetch(PDO::FETCH_ASSOC);
            return $row['_id'];
        }
        public static function ObtenerTodosNoListos(){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDatos->prepararConsulta("SELECT * from pedido_producto WHERE pedido_producto._estado != 2");
            $query->execute();

            return $query->fetchAll(PDO::FETCH_CLASS, 'Pedido_Producto');
        }
        public static function ObtenerTodosLosListos(){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDatos->prepararConsulta("SELECT * from pedido_producto WHERE pedido_producto._estado = 2");
            $query->execute();

            return $query->fetchAll(PDO::FETCH_CLASS, 'Pedido_Producto');
        }
    
    
    }



?>