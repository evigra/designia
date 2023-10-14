<?php		
	if(!isset($_SESSION))
	{
		$usuarios_sesion						="PHPSESSID";
		session_name($usuarios_sesion);
		@session_start();
		@session_cache_limiter('nocache,private');			
		/*
		if(count($_COOKIE) > 0 AND isset($_COOKIE["solesgps"])) 
			$_SESSION=$_COOKIE["solesgps"];
		*/		
	}
	
	if($_REQUEST["datos"])	
	{		
		$vdatos								=explode(":", $_REQUEST["datos"]);
		$_REQUEST["user"]					=$vdatos[0];

		if($vdatos[1]!="")	$_REQUEST["path"]=$vdatos[1];
		else 				$_REQUEST["path"]="Events/";

		$vpath=explode("/", $_REQUEST["path"]);
		$_REQUEST["class"]=$vpath[0];

		$vserver=explode(".", $_SERVER["HTTP_HOST"]);
		$_REQUEST["server"]	=$vserver[1] . "." .$vserver[2];

		unset($_REQUEST["datos"]);
	}
	

	if(!isset($_SESSION))		$_SESSION=array();
	
	if(isset($_SESSION))
	{

		if(!isset($_SESSION["var"]))			$_SESSION["var"]					=array();
						
		$_SESSION["var"]["false"]			=array(0,"0","false", "no");
		$_SESSION["var"]["true"]			=array(1,"1","true", "yes","si");
		$_SESSION["var"]["server_true"]		=array("www.solesgps.com","solesgps.com","www.soluciones-satelitales.com","soluciones-satelitales.com");
		$_SESSION["var"]["server_error"]	=array("localhost","developer.solesgps.com");		
		$_SESSION["var"]["server"]			=array_merge($_SESSION["var"]["server_true"], $_SESSION["var"]["server_error"]);
			
		if(@$_GET["sys_action"]=="cerrar_sesion")
		{
			session_destroy();
			$destino= "../sesion/";	
			Header ("Location: $destino");			
		}	
	}
	$pre_path="";	
?>
