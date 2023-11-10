<?php

	require_once("nucleo/basededatos.php");	
	require_once("nucleo/auxiliar.php");	
	require_once("nucleo/general.php");	

	require_once("nucleo/sesion.php");	

	$path_controlador="controlador/" . $_REQUEST["class"] . ".php";
	$path_modelo="modelo/" . $_REQUEST["class"] . ".php";

	if(isset($_REQUEST["abrev"]))
	{
		$objeto			=new general();
		$comando_sql	="
			SELECT *, 
				e.id as event_id,
				f.id as file_id
			FROM 
				events e JOIN 
				files f ON e.id=f.event_id JOIN
				user u ON e.user_id=u.id 
			WHERE
				MD5(f.id)='{$_REQUEST["abrev"]}'
		";
		$file=$objeto->__EXECUTE($comando_sql)[0];
		$title="";
		if($file["title"]!="")		
		{
			$title_url				=str_replace(" ", "_", $file["title"]);   
			$title_url				=urlencode($title_url);
			$title_url				=str_replace("%", "_", $title_url);
			$title_url				=str_replace("/", "_", $title_url);					
		}
		else	$title_url			="Evento";

		$path="http://" . $_SERVER["SERVER_NAME"] . "/Photo/$title_url/&event=" . MD5($file["event_id"]) . "&file=" . MD5($file["file_id"]) ;
		Header ("Location: $path");			
	}
	else if(@file_exists($path_controlador))		
	{	    	
		if(@file_exists($path_modelo))			require_once($path_modelo);			
		require_once($path_controlador);			
	}	
	else
		echo "ERROR: La pagina que ingresaste no existe";	

		

?>