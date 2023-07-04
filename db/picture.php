<?php

class Picture{
    public static function GuardarImagen($nombreFoto, $destino){     
        $dir = "";
        if(isset($_FILES["foto"]) && file_exists($destino)){
            $dir = ".".DIRECTORY_SEPARATOR.$destino.DIRECTORY_SEPARATOR.$nombreFoto.".".pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
            move_uploaded_file($_FILES["foto"]["tmp_name"], $dir); 
        }
        return strval($dir);
    }

    public static function CopiarImagen($antiguoDir, $destino) {
        if(file_exists($destino)){
            $nombreFoto = explode("".DIRECTORY_SEPARATOR."", $antiguoDir)[2];
            $dir =  ".".DIRECTORY_SEPARATOR.$destino.DIRECTORY_SEPARATOR.$nombreFoto;
            copy($antiguoDir, $dir);
            return $dir;
        }
        return "";
    }
    
    public static function MoverImagen($antiguoDir, $nuevoDir){
        $nombreFoto = explode("".DIRECTORY_SEPARATOR."", $antiguoDir)[2];        
        return rename($antiguoDir, $nuevoDir.DIRECTORY_SEPARATOR.$nombreFoto);
    } 
}
?>