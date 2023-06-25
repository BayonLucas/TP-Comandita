<?php
    include_once './db/AccesoDatos.php';

    class Usuario{
        
        public $_id;
        public $_rol; //Bartender, Socio, cocinero, mozo, cervecero
        public $_nombre;
        public $_dni;
        public $_estado; //0 = Libre - 1 = Ocupado
        public $_fechaRegistro;
        public $_fechaBaja;

        // public function __construct($rol, $nombre, $dni){
        //     $this->_id = null;
        //     $this->_rol = $rol;
        //     $this->_nombre = $nombre;
        //     $this->_dni = $dni;
        //     $this->_estado = 0; 
        //     $this->_fechaRegistro = date("y-m-d");
        //     $this->_fechaBaja = null;
        // }

        public function CrearUsuario(){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (_rol, _nombre, _dni, _estado, _fechaRegistro, _fechaBaja) VALUES (:rol, :nombre, :dni, :estado, :fechaRegistro,  :fechaBaja)");
            $query->bindValue(':rol', $this->_rol, PDO::PARAM_STR);
            $query->bindValue(':nombre', $this->_nombre, PDO::PARAM_STR);
            $query->bindValue(':dni', $this->_dni, PDO::PARAM_STR);
            $query->bindValue(':estado', $this->_estado, PDO::PARAM_INT);
            $query->bindValue(':fechaRegistro', $this->_fechaRegistro);
            $query->bindValue(':fechaBaja', $this->_fechaBaja);
            $query->execute();

            return $objAccesoDatos->obtenerUltimoId();
        }

        public static function ObtenerTodos(){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios");
            $query->execute();

            return $query->fetchAll(PDO::FETCH_CLASS, 'Usuario');
        }

        public static function ObtenerPorRol($rol){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios WHERE _rol = :rol");
            $query->bindValue(':rol', $rol, PDO::PARAM_STR);

            $query->execute();

            return $query->fetchAll(PDO::FETCH_CLASS, 'Usuario');
        }

        public static function ObtenerUsuario($id){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios WHERE _id = :id");
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();

            return $query->fetchObject('Usuario');
        }

        public static function ModificarUsuario($id, $estado = null, $fechaBaja = null){
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $queryStr = "UPDATE usuarios SET ";
            $params = array(':id' => $id);
        
            if($estado !== null){
                $queryStr .= "_estado = :estado";
                $params[':estado'] = $estado;
            }
        
            if($fechaBaja !== null){
                if($estado !== null){
                    $queryStr .= ", ";
                }
                $queryStr .= "_fechaBaja = :fechaBaja";
                $params[':fechaBaja'] = null;
            }
        
            $queryStr .= " WHERE _id = :id";
            $query = $objAccesoDato->prepararConsulta($queryStr);
            
            foreach ($params as $key => &$value) {
                if(is_int($value)){
                    $query->bindParam($key, $value, PDO::PARAM_INT);
                } 
                else{
                    $query->bindParam($key, $value);
                }
            }
            $query->execute();
            /*$objAccesoDato = AccesoDatos::obtenerInstancia();
            if($estado != null && $fechaBaja != null){
                $query = $objAccesoDato->prepararConsulta("UPDATE usuarios SET _estado = :estado, _fechaBaja = :fechaBaja  WHERE _id = :id");
                $query->bindValue(':estado', $estado, PDO::PARAM_STR);
                $query->bindValue(':fechaBaja', null);

            }
            else if($estado == null && $fechaBaja != null){
                $query = $objAccesoDato->prepararConsulta("UPDATE usuarios SET _fechaBaja = :fechaBaja WHERE _id = :id");
                $query->bindValue(':fechaBaja', null);

            }
            else{
                $query = $objAccesoDato->prepararConsulta("UPDATE usuarios SET _estado = :estado WHERE _id = :id");
                $query->bindValue(':estado', $estado, PDO::PARAM_STR);

            }
            $query->bindValue(':id', $id, PDO::PARAM_INT);

            $query->execute();
            */
        }

        public static function BorrarUsuario($id){
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $query = $objAccesoDato->prepararConsulta("UPDATE usuarios SET _fechaBaja = :fechaBaja WHERE _id = :id");
            $fecha = new DateTime(date("d-m-Y"));
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->bindValue(':fechaBaja', date_format($fecha, 'y-m-d H:i:s'));
            $query->execute();
        } 
    }
?>