<?php

	require_once("nucleo/basededatos.php");	
	require_once("nucleo/auxiliar.php");	
	require_once("nucleo/general.php");	

	require_once("nucleo/sesion.php");	

	$path_index="modulos/" . $_REQUEST["class"] . "/index.php";
	$path_model="modulos/" . $_REQUEST["class"] . "/modelo.php";

	if(@file_exists($path_index))		
	{	    	
		require_once($path_model);			
		require_once($path_index);			
	}
	else
		echo "ERROR: La pagina que ingresaste no existe";	
?>