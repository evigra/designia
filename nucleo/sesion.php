<?php		
	
	if($_REQUEST["datos"])	
	{		
		$vdatos								=explode(":", $_REQUEST["datos"]);
		$_REQUEST["user"]					=$vdatos[0];

		//if(@$vdatos[1]=="")					$vdatos[1]="Portada/Show/";
		if(@$vdatos[1]=="")					$vdatos[1]="Chilaquil/Show/";
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


	if(isset($_COOKIE["designia"]))
	{
		setcookie("designia", $_COOKIE["designia"], time()+ (60 * 60 * 24 *7), "/", $_REQUEST["server"]);

		if(!isset($_SESSION))
		{
			$usuarios_sesion						="PHPSESSID";
			session_name($usuarios_sesion);
			session_start();
			#session_cache_limiter('nocache,private');			
		}

		if(!isset($_SESSION["user"]))
		{

			$path_model="modulos/Sesion/modelo.php";		
			require_once($path_model);			
			$objeto			=new sesion();
			$objeto->__COOKIE();			
		}

	
	}

	


	if(@$_GET["sys_action"]=="cerrar_sesion")
	{
		if(isset($_SESSION))
		{			
			session_destroy();			
		}	
		if(isset($_COOKIE))
		{							
			setcookie("designia", $_COOKIE["designia"], time() - (3600), "/", $_REQUEST["server"]);
			unset($_COOKIE['designia']);
			//setcookie("designia", "", time() - 3600, "/");
		}	
		Header ("Location: http://" . $_REQUEST["user"] . "." . $_REQUEST["server"] . "/Chilaquil/Show/");			
	}
	
	$pre_path="";	
?>