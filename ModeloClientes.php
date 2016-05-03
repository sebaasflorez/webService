<?php

require_once 'Modelo.php';

class ModeloClientes extends Modelo {

    function ModeloClientes() {
        parent::__construct();
    }

    function ListarClientes($filtro='') {

    	$_filtro = (isset($filtro['filtro']))?"nombres like '%".$filtro['filtro']."%'":'';

       return  $this->select(array('tipo_documento','numero_documento','nombres','apellidos','telefono','direccion'),'clientes',$_filtro); 
    }

    function CrearCliente($parametros){

    	$tipo = (isset($parametros['tipo_documento']))?$parametros['tipo_documento']:'CC';
    	$numero = (isset($parametros['numero_documento']))?$parametros['numero_documento']:'0';
    	$nombres = (isset($parametros['nombres']))?$parametros['nombres']:'';
    	$apellidos = (isset($parametros['apellidos']))?$parametros['apellidos']:'';
    	$telefono = (isset($parametros['telefono']))?$parametros['telefono']:'';
    	$direccion = (isset($parametros['direccion']))?$parametros['direccion']:'';
    	
    	$campos = array(
    		'tipo_documento'   => $tipo,
    		'numero_documento' => $numero,
    		'nombres' => $nombres,
    		'apellidos' => $apellidos,
    		'telefono' => $telefono,
    		'direccion' => $direccion
    	);

    	$crear =  $this->insert($campos,'clientes');

    	if ($crear)
    	    return 'OK';
    	else
    		return 'Error';

    }

    function EditarCliente($parametros){

    	if (isset($parametros['cliente']) && $parametros['cliente']!='') {
    		
    		$cliente = $parametros['cliente'];
    		unset($parametros['cliente']);
    		unset($parametros['funcion']);

    		foreach ($parametros as $key => $value) {
    			$campos[$key] = $value;
    		}
    		
    		$editar = $this->update($campos,'clientes','numero_documento='.$cliente);
    		if ($editar)
    	    	return 'OK';
    		else
    			return 'Error';

    	}else{
    		return 'Cliente Invalido';
    	}

    }

}
?>


