<?php
	class auxiliar extends basededatos 
	{   
		##############################################################################	
		##  Propiedades	
		##############################################################################
		var $html		="";
		var $words		=Array(
			"html_head_title" 	=>"Designia :: ",
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
		public function __VIEW_MODULE($path,$words)
		{ 
			$template	=$this->__TEMPLATE($path);
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
			}
		    return $return;
		}						
		##############################################################################	    
		///////////////////////////////////////////////////////////


		function __PRINT_R($variable)
		{  
		    echo "<div class=\"developer\" title=\"Sistema \"><pre>";
		    @print_r(@$variable);
		    echo "</pre></div>";		    			
    	} 
		
	}  	
?>
