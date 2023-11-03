<?php
	class sesion extends general
	{   
		##############################################################################	
		##  Propiedades	
		##############################################################################

		##############################################################################	
		##  Metodos	
		##############################################################################
        
		public function __CONSTRUCT($option=null)
		{	
			$this->words["html_form_sesion"]="";	
			if(isset($_REQUEST["email"]))
			{
				$comando_sql	="
					SELECT *
					FROM user
					WHERE 
						email='{$_REQUEST["email"]}'
				";
				
				$user=$this->__EXECUTE($comando_sql);								
				if(isset($user[0]) and isset($user[0]["email"]) and $user[0]["email"]==$_REQUEST["email"] and $user[0]["password"]==$_REQUEST["password"])
				{
					setcookie("designia", md5($user[0]["id"]), time()+ (60 * 60 * 24 * 7), "/", $_REQUEST["server"]);
					
					$usuarios_sesion						="PHPSESSID";
					session_name($usuarios_sesion);
					session_start();
					session_cache_limiter('nocache,private');			
					$_SESSION["user"]			=$user[0];										
					

					$_SESSION["user"]["first_name"]		=explode(" ",$_SESSION["user"]["name"])[0];

					Header ("Location: ../../Chilaquil/Show/");
				}
				else
				{
					$this->words["html_form_sesion"]="El correo electronico no esta asociado a ninguna cuenta";
				}
			}

			return parent::__CONSTRUCT($option);
		}

		public function __COOKIE()
		{	
			$comando_sql	="
				SELECT MD5(id) as md5_id, 
				u.*
				FROM user u
				WHERE 
					md5(id)='{$_COOKIE["designia"]}'
			";
			
			$user	=$this->__EXECUTE($comando_sql);								
			if(isset($user[0]) and isset($user[0]["md5_id"]) and $user[0]["md5_id"]==$_COOKIE["designia"])
			{				

				$_SESSION["user"]=$user[0];										
				$_SESSION["user"]["first_name"]		=explode(" ",$_SESSION["user"]["name"])[0];
			}
		}		
	}
?>