<?php
	class general extends auxiliar
	{   
		##############################################################################	
		##  Metodos	
		##############################################################################		
		public function __CONSTRUCT()
		{		
			$files_available				=array("image/png","image/jpeg");

			$this->words["server"]			=$_REQUEST["server"];
			$this->words["user"]			=$_REQUEST["user"];
			$this->words["path"]			=$_REQUEST["path"];
			$this->__FILES_DATA				=array();	
			$this->words["html_create"]		=$this->__VIEW_BASE("cargar", $this->words);

			if(isset($_FILES["files"]))
			{	
				$comando_sql	="INSERT INTO events (user_id, type, datetime_show, datetime, title, description)
					VALUES( 
						'1', 
						'" . $_REQUEST["class"]. "',
						'" . date("Y-m-d H:i:s") . "',
						'" . date("Y-m-d H:i:s"). "',
						'" . $_REQUEST["title"]. "',
						'" . $_REQUEST["description"]. "'
					)
				";
				$events_id			=$this->__EXECUTE($comando_sql);	

				foreach($_FILES["files"] as $field => $values)
				{		        
					foreach($values as $row => $data) 
					{
						if(in_array($_FILES["files"]["type"][$row], $files_available))
						{
							if($field=="name")
							{
								$vdata			=explode(".", $data);

								$comando_sql	="INSERT INTO files (event_id, user_id, extension, temp)
								VALUES(	
									'$events_id', 
									'1', 
									'" . $vdata[count($vdata)-1] ."',
									'" . $_FILES["files"]["tmp_name"][$row] . "'
								)";
								$file_id								=$this->__EXECUTE($comando_sql);					
								$this->__FILES_DATA[$row]["id"]			=$file_id;
								$this->__FILES_DATA[$row]["extension"]	=$vdata[count($vdata)-1];
								$this->__FILES_DATA[$row]["copiado"]	=0;


							}						
							$this->__FILES_DATA[$row][$field]=$data;
						}	
					}
				}				
				$this->__FILES_COPI();
				
			}
		}
		public function __FILES_COPI()
    	{    	
			foreach($this->__FILES_DATA as $row=>$file)
			{
				if($file["copiado"]==0)
				{
					$maximo=600;
					$path="modulos/files/file/";
					$archivo =$path . "file_" . md5($file["id"]) . "." . $file["extension"];
					$archivo_r =$path . "r-_file_" . md5($file["id"]) . "." . $file["extension"];
					//move_uploaded_file($file["tmp_name"], $path . $this->__FILES_DATA[$row]["id"] . "." . $this->__FILES_DATA[$row]["extension"]);
					
					$im 			= new imagick($file["tmp_name"]);
					$imageprops 	= $im->getImageGeometry();
					$width 			= $imageprops['width'];
					$height 		= $imageprops['height'];

					if($width > $height)
					{
						$newHeight = $maximo;
						$newWidth = ($maximo / $height) * $width;
					}else{
						$newWidth = $maximo;
						$newHeight = ($maximo / $width) * $height;
					}
					$im->resizeImage($newWidth,$newHeight, imagick::FILTER_LANCZOS, 0.8, true);
					#$im->cropImage (280,280,0,0);
					// Escribimos la nueva imagen redimensionada
					#$im->writeImage( $archivo );	
					
					##############################

					$wm = new Imagick();
					$wm->readImage("logo.png") or die("Couldn't load $wm");
					$im->compositeImage($wm, imagick::COMPOSITE_OVER, 0, 0);
					$im->writeImage( $archivo );	


					##############################

					/*
					if(move_uploaded_file($file["tmp_name"], $archivo))
					{
						$this->__FILES_DATA[$row]["copiado"]=1;
					}
					*/					
				}
			}

		}		


		##############################################################################		 		
		public function __BROWSE()
    	{    	
    	}		
		##############################################################################		 		
		public function __SAVE()
    	{
    	}    	
    	##############################################################################	   	
		public function __DELETE()
    	{
		}
    	##############################################################################	   	
		public function __EXECUTE($comando_sql, $option=array("open"=>1,"close"=>1))
    	{
    		if(is_string($option))
    		{
    			$option=array("open"=>1,"close"=>1);
    		}
    	
    		$return=array();    		    		
    		
    		if(@$this->sys_sql=="") 		$this->sys_sql=$comando_sql;
    		
	   		if(is_array($option))
    		{
				if(isset($option["echo"]))  
				{
		        	echo "<div class=\"echo\" style=\"display:none;\" title=\"{$option["echo"]}\">".$this->comando_sql."</div>";
		        }	
    			if(isset($option["open"]))	
    			{    			
    				$this->abrir_conexion();
    				if(isset($option["e_open"])  AND $this->sys_enviroments	=="DEVELOPER" AND @$this->sys_private["action"]!="print_pdf")
    					echo "<br><b>CONECCION ABIERTA</b><br>$comando_sql<br>{$option["e_open"]}";    				
    			}	
    		}

			$row=0;				
			if(is_object($this->OPHP_conexion)) 
			{
				$resultado	= @$this->OPHP_conexion->query($comando_sql);

				
				
				if(isset($this->OPHP_conexion->error)  AND $this->OPHP_conexion->error!="")
				{					
					echo "
						<div class=\"echo\" style=\"display:none;\" title=\"ERROR {$this->sys_object}\">
							{$this->OPHP_conexion->error}
							<br><br>
							$comando_sql
						</div>
					";
				}						
			}	
			else
			{
				$resultado=array();
				if(isset($option["echo"]) )
					echo "<div class=\"echo\" style=\"display:none;\" title=\"Coneccion\">Error en la conecion</div>";
			}	

						
			if(is_object(@$resultado)) 
			{			
				while($datos = $resultado->fetch_assoc())
				{							
					foreach($datos as $field =>$value)
					{
						if(is_string($field) AND !is_null($field))
						{
							#$value 					= html_entity_decode($value);
							$return[$row][$field]	=$value;
						}	
					}		
					$row++;	
				}
				$resultado->free();					
			}

			if(substr($comando_sql, 0, 6)=="INSERT")
				$return	=$this->OPHP_conexion->insert_id;
			
			#

			$this->__MESSAGE_EXECUTE="";
    		if(is_array($option))
    		{
    			if(isset($option["close"]))	
    			{
    				@$this->cerrar_conexion();
    				    if(isset($option["e_close"]) AND in_array($_SERVER["SERVER_NAME"],$_SESSION["var"]["server_error"]) AND @$this->sys_private["action"]!="print_pdf")
    					echo "<br><b>CONECCION CERRADA</b><br>{$option["e_close"]}";
    			}	
    		}
       		return $return;	
    	}    	   		
	}
?>
