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
					$_SESSION["user"]=$user[0];					
					Header ("Location: ../../Chilaquil/Show/");			
				}
				else
				{
					$this->words["html_form_sesion"]="El correo electronico no esta asociado a ninguna cuenta";

				}
			}

			return parent::__CONSTRUCT($option);
		}
	}
?>
