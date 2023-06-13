<?php
    include_once "AccesoDatos.php";

    class Usuario implements ICrud{
        
        public $_id;
        public $_rol; //Bartender, Socio, cocinero, mozo, cervecero
        public $_nombre;
        public $_dni;
        public $_estado;
        public $_fechaRegistro;
        public $_fechaBaja;

        public function __construct($rol, $nombre, $dni){
            $this->_id = null;
            $this->_rol = $rol;
            $this->_nombre = $nombre;
            $this->_dni = $dni;
            $this->_estado = 0; //0 = Libre - 1 = Ocupado
            $this->_fechaRegistro = date("d-m-Y");
            $this->_fechaBaja = null;
        }

        public function CrearUsuario(){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (rol, nombre, dni, estado, fechaRegistro,  fechaBaja) VALUES (:rol, :nombre, :dni, :estado, :fechaRegistro,  :fechaBaja)");
            $consulta->bindValue(':rol', $this->_rol, PDO::PARAM_STR);
            $consulta->bindValue(':nombre', $this->_nombre, PDO::PARAM_STR);
            $consulta->bindValue(':dni', $this->_dni, PDO::PARAM_STR);
            $consulta->bindValue(':estado', $this->_estado, PDO::PARAM_STR);
            $consulta->bindValue(':fechaRegistro', $this->_fechaRegistro);
            $consulta->bindValue(':fechaBaja', $_fechaBaja);
            $consulta->execute();

            return $objAccesoDatos->obtenerUltimoId();
        }

        public static function ObtenerTodos(){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios");
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
        }

        public static function ObtenerUsuario($id){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios WHERE usuario.id = :id");
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
            $consulta->execute();

            return $consulta->fetchObject('Usuario');
        }

        public static function ModificarUsuario($id, $estado){
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET estado = :estado WHERE id = :id");
            $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
            $consulta->execute();
        }

        public static function BorrarUsuario($id){
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fechaBaja = :fechaBaja WHERE id = :id");
            $fecha = new DateTime(date("d-m-Y"));
            $consulta->bindValue(':id', $usuario, PDO::PARAM_INT);
            $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
            $consulta->execute();
        } 
    }
?>