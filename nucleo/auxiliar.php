<?php
	class auxiliar extends basededatos 
	{   
		##############################################################################	
		##  Propiedades	
		##############################################################################
		var $html		="";
		var $words		=Array(
			"html_head_title" 	=>"Designia :: ",
			"html_create" 		=>"",
		);
	

		##############################################################################	
		##  METODOS	
		##############################################################################
		public function __VIEW($path,$words)
		{ 
			$template	=$this->__TEMPLATE($path);
			return $this->__REPLACE($template,$words);
    	}
		##############################################################################
		public function __VIEW_MODULE($path,$words=array())
		{ 
			$template	=$this->__TEMPLATE("modulos/".$_REQUEST["path"] . "html/".$path);
			return $this->__REPLACE($template,$words);
    	}
		##############################################################################
		public function __VIEW_BASE($path,$words)
		{ 
			$template	=$this->__TEMPLATE("sitio_web/html/".$path);
			return $this->__REPLACE($template,$words);
    	}
		##############################################################################    	 
		function __REPLACE($str,$words)
		{  
			if(is_array($words))
			{
				$return								=$str;
				foreach($words as $word=>$replace)
				{		        	
					$return							=str_replace("{".$word."}", $replace, $return);     	    	
				}
			}	
			else
				$return								="ERROR:: La funcion __REPLACE necesita un array para remplazar";
			return $return;
		} 		
		##############################################################################
		function __TEMPLATE($form=NULL)
		{
			# RETORNA UNA CADENA, QUE ES LA PLANTILLA
			# DE LA RUTA ENVIADA		
	    	if(!is_null($form))
	    	{
	    		$return="";
	    		
	    		$archivo = $form.'.html';
	    		if(@file_exists($archivo))			    			
		    		$return 						= file_get_contents($archivo);		    
	    		elseif(@file_exists("../".$archivo))			    			
		    		$return 						= file_get_contents("../".$archivo);		    		    		
	    		elseif(@file_exists("../../".$archivo))			    			
		    		$return 						= file_get_contents("../../".$archivo);		    		    		
	    		elseif(@file_exists("../../../".$archivo))			    			
		    		$return 						= file_get_contents("../../../".$archivo);		    		    		
	    		elseif(@file_exists("../../../../".$archivo))			    			
		    		$return 						= file_get_contents("../../../../".$archivo);	
				else
					$return ="Path no encontrado: " .$archivo;	    		    				    		
			}
		    return $return;
		}						
		##############################################################################	    
		///////////////////////////////////////////////////////////

		function __PERFIL_DATA($row)
		{  
			$return=array();
			if(isset($row["name"]))				$return["perfil_name"]=$row["name"];
			if(isset($row["user"]))				$return["perfil_user"]=$row["user"];
			if(isset($row["datetime_show"]))	$return["perfil_date"]=$row["datetime_show"];
			if(isset($row["type"]))				$return["perfil_type"]=$row["type"];

			$return["perfil_url"]				="http://". $row["user"] . "." . $_REQUEST["server"];

			return $return;
		} 
		public function __SOCIAL_NETWORKS($url, $file_id)
    	{    	
			$aux=rawurlencode($url);
			$url=rawurlencode("http://" . $_SERVER["SERVER_NAME"] . "/&abrev=$file_id");
						
			$return="";

			$return.="<a href=\"https://www.facebook.com/dialog/share?
			app_id=1984430411957885
			&display=popup
			&href=$aux"."&type=custom_url&app_absent=0\" target=\"_blank\">
				<img width=\"40\" src=\"../../sitio_web/img/facebook.svg\">";

			$return.="<a href=\"https://twitter.com/intent/tweet?url=$url\" target=\"_blank\">
				<img width=\"40\" src=\"../../sitio_web/img/twiter.jpg\">";

			$return.="<a href=\"https://api.whatsapp.com/send/?text=$url\" target=\"_blank\">
				<img width=\"45\" src=\"../../sitio_web/img/WhatsApp.png\">";		

			$return.="<a href=\"https://www.pinterest.com/pin/create/button/?url=$url"."&type=custom_url&app_absent=0\" target=\"_blank\">
				<img width=\"40\" src=\"../../sitio_web/img/pinterest.png\">";

			$return.="<a href=\"https://www.reddit.com/submit?url=$url"."&type=custom_url&app_absent=0\" target=\"_blank\">
				<img width=\"40\" src=\"../../sitio_web/img/reddit.png\">";

			$return.="<a class=\"acortador\" title=\"http://" . $_SERVER["SERVER_NAME"] . "/&abrev=$file_id\"  target=\"_blank\">
				<img width=\"40\" src=\"../../sitio_web/img/acortador.jpg\">";
					
			return $return; 
		}
		public function __REDIMENSION($maximo, $width, $height)
    	{    	
			if($width > $maximo)
			{
				$aux	=$width;
				$width	=$maximo;
				$height	=($maximo * $height) / $aux;  		
			}			
			if($height>$maximo)
			{
				$aux	=$height;
				$height	=$maximo;
				$width	=($maximo * $width) / $aux;  		
			}
			return array(round($height), round($width));
		}		
		public function __FILES_COPI()
    	{    	
			foreach($this->__FILES_DATA as $row=>$file)
			{
				$vtype			=explode("/", $file["type"]);
				$type			=$vtype[0];				
			}
		}		
		public function __MENU_SESSION()
    	{    	
			if(isset($_SESSION["user"]))
			{
				$this->words["html_sesion_first_name"]	=$_SESSION["user"]["first_name"];	
				$this->words["html_create"]				=$this->__VIEW_BASE("cargar", $this->words);				
				
				$this->words["html_sesion"]				="
					<div class=\"menu_imagen\" ><img class=\"menu_imagen\" src=\"../../sitio_web/img/personas.png\" ></div>
					<div class=\"menu_texto\" style=\"color:#fff;\" >{$_SESSION["user"]["name"]}</div>
					
					<div class=\"menu_separador\"></div>
					<a href=\"&sys_action=cerrar_sesion\" style=\"color:#fff;\"> 
						<div class=\"menu_imagen\" ><img class=\"menu_imagen\" src=\"../../sitio_web/img/salida.png\" ></div>
						<div class=\"menu_texto\"  >Cerrar</div>
					</a>
				";
			}
			else
			{
				$this->words["html_sesion"]		="
					<a href=\"../../Sesion/Create/\" style=\"color:#fff;\"> 						
						<div class=\"menu_imagen\" ><img class=\"menu_imagen\" src=\"../../sitio_web/img/entrada.png\" ></div>
						<div class=\"menu_texto\" >Login</div>
					</a>
				";
			}
		}
		


	}  	
?>