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

		if(@$vdatos[1]=="")					$vdatos[1]="Portada/Show/";
		if($vdatos[1]!="")	
		{
			$vpath							=explode("/", $vdatos[1]);	
			#if(count($vpath)>1)
			{
				$_REQUEST["path"]			=$vpath[0] . "/";
				$_REQUEST["method"]			=$vpath[1];
				$_REQUEST["class"]			=$vpath[0];
			}
		}

		if($_REQUEST["user"]=="Sociales/Show/" and $_SERVER["HTTP_HOST"]=="designia.vip")
		{	
			$_REQUEST["user"]="wwww";			
			$_SERVER["HTTP_HOST"]="http://".$_REQUEST["user"] .".". $_SERVER["HTTP_HOST"];
			Header ("Location: {$_SERVER["HTTP_HOST"]}");			
		}
		if($_REQUEST["user"]=="Sociales/Show/" and $_SERVER["HTTP_HOST"]=="designia.localhost")
		{			
			$_REQUEST["user"]="wwww";	
			$_SERVER["HTTP_HOST"]="http://".$_REQUEST["user"] .".". $_SERVER["HTTP_HOST"];		
			Header ("Location: {$_SERVER["HTTP_HOST"]}");
		}
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
			//$destino= "designia.vip";	
			Header ("Location: http://{$_REQUEST["server"]}/Sociales/Show/");			
		}	
	}
	$pre_path="";	
?>
