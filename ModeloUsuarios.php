<?php

require_once 'Modelo.php';

class ModeloUsuarios extends Modelo {

    function ModeloUsuarios() {
        parent::__construct();
    }

    function ValidarUsuario($parametros) {

        $usuario = isset($parametros['nickname'])?$parametros['nickname']:'' ;
        $clave = isset($parametros['clave'])?$parametros['clave']:'';

       $login = $this->select(array('id_usuario'),'usuarios',"nickname='".$usuario."' AND clave='".$clave."'"); 

       if ($login)
            return 'OK';
       else
            return 'Invalido';
       
    }


}
?>


