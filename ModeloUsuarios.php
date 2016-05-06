<?php

require_once 'Modelo.php';

class ModeloUsuarios extends Modelo {

  function ModeloUsuarios() {
    parent::__construct();
  }

  function ValidarUsuario($parametros) {

    $usuario = isset($parametros['nickname'])?$parametros['nickname']:'' ;
    $clave = isset($parametros['clave'])?$parametros['clave']:'';

    $login = $this->selectOne(array('nombres','apellidos'),'usuarios',"nickname='".$usuario."' AND clave='".$clave."'"); 

    if ($login){
      $respuesta['salida'] = $login['nombres'].' '.$login['apellidos'];
      $respuesta['estado'] = '1';
      return $respuesta;

    }else {

     $respuesta['salida'] = 'Error';
     $respuesta['estado'] = '2';
     return $respuesta;
   }

 }


}
?>

