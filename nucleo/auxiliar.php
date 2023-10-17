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

		
	}  	
?>
