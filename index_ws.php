<?php 

	ini_set("display_errors",0);
    ini_set("html_errors",0);

  if (isset($_GET['funcion'])) {

  	switch ($_GET['funcion']) {

  		case 'reporte_clientes': //http://localhost/ws_sso/index_ws.php?funcion=reporte_clientes&filtro=Se

  			require_once "ModeloClientes.php";
    		$modelo = new ModeloClientes();
    		$listado = $modelo->ListarClientes($_GET);
    		echo json_encode($listado);

  			break;

  		case 'crear_cliente':  //http://localhost/ws_sso/index_ws.php?funcion=crear_cliente&nombres=Maria&apellidos=Cardona&numero_documento=1090076711&direccion=Manizales&telefono=312806057
  			require_once "ModeloClientes.php";
    		$modelo = new ModeloClientes();
    		echo   json_encode($modelo->CrearCliente($_GET));

  			break;

  		case 'editar_cliente':  //http://localhost/ws_sso/index_ws.php?funcion=crear_cliente&nombres=Maria&apellidos=Cardona&numero_documento=1090076711&direccion=Manizales&telefono=312806057
  			require_once "ModeloClientes.php";
  			$modelo = new ModeloClientes();
  			echo   json_encode($modelo->EditarCliente($_GET));

  			break;

  		case 'login':  //http://localhost/ws_sso/index_ws.php?funcion=login&nickname=sebas&clave=111111
  			require_once "ModeloUsuarios.php";
  			$modelo = new ModeloUsuarios();
  			echo   json_encode($modelo->ValidarUsuario($_GET));

  			break;
  		
  		default:
  			echo "Funcion invalida";
  			break;
  	}
  	
  }else{
  	echo "Funcion invalida";
  }
?>