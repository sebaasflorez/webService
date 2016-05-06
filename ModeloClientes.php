<?php

require_once 'Modelo.php';

class ModeloClientes extends Modelo {

    function ModeloClientes() {
        parent::__construct();
    }

    function ListarClientes($filtro='') {
        $_filtro = (isset($filtro['filtro']))?"nombres like '%".$filtro['filtro']."%'":'';
        return  $this->select(array('id_cliente','tipo_documento','numero_documento','nombres','apellidos','telefono','direccion'),'clientes',$_filtro); 
    }

    function BuscarCliente($filtro){
        if (isset($filtro['cliente'])) {
            $_filtro = "id_cliente = ".$filtro['cliente'];
            return  $this->selectOne(array('id_cliente','tipo_documento','numero_documento','nombres','apellidos','telefono','direccion'),'clientes',$_filtro); 
        }else{
            return 'Cliente Invalido';
        }
        

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
    		
    		$editar = $this->update($campos,'clientes','id_cliente='.$cliente);
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


