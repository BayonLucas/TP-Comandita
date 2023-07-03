<?php

use Firebase\JWT\JWT;
use Firebase\JWT\key;

class Token{
    private static $claveSecreta = 'Lu$B4JwT';
    private static $tipoEncriptacion = ['HS256'];

    public static function CrearToken($id, $rol, $admin = false){
        $ahora = time();
        $payload = array(
            'iat' => $ahora,
            'exp' => $ahora + (60000)*24*90,
            'aud' => self::Aud(),
            'idUsuario' => $id,
            'rol' => $rol,
            'admin' => $admin,
        );
        return JWT::encode($payload, self::$claveSecreta);
    }

    public static function VerificarToken($token){
        if (empty($token)) {
            throw new Exception("El token esta vacio.");
        }
        try {
            $decodificado = JWT::decode($token, self::$claveSecreta, self::$tipoEncriptacion);
        } catch (Exception $e) {
            throw $e;
        }
        if ($decodificado->aud !== self::Aud()) {
            throw new Exception("No es el usuario valido");
        }
    }

    public static function ObtenerPayLoad($token){
        if (empty($token)) {
            throw new Exception("El token esta vacio.");
        }
        return JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
        );
    }

    public static function ObtenerRol($token){
        return JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
        )->rol;
    }
    public static function ObtenerIdUsuario($token){
        return JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
        )->idUsuario;
    }

    private static function Aud(){
        $aud = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }

        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();

        return sha1($aud);
    }
}
