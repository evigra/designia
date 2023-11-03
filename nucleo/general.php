<?php
	class general extends auxiliar
	{   
		##############################################################################	
		##  Metodos	
		##############################################################################		
		public function __CONSTRUCT()
		{		
			$files_available				=array("image/png","image/jpeg", "video/mp4");

			$this->words["server"]			=$_REQUEST["server"];
			$this->words["user"]			=$_REQUEST["user"];
			$this->words["path"]			=$_REQUEST["path"];
			$this->__FILES_DATA				=array();	

			#$this->__PRINT_R(@$_SESSION, "_SESSION");
			#$this->__PRINT_R(@$_COOKIE, "_COOKIE");

			
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
					
				#$this->__PRINT_R($_FILES["files"]);
				foreach($_FILES["files"] as $field => $values)
				{		        
					foreach($values as $row => $data) 
					{
						if(in_array($_FILES["files"]["type"][$row], $files_available))
						{
							$width 			= 0;
							$height 		= 0;

							if($field=="name")
							{
								$path="modulos/files/file/";
								###
								if(!isset($events_id)) $events_id			=$this->__EXECUTE($comando_sql);

								$newHeight 		= 0;
								$newWidth 		= 0;
								$orientation 	= "";

								$temporal		=$_FILES["files"]["tmp_name"][$row];

								$vname			=explode(".", $_FILES["files"]["name"][$row]);
								$extencion		=$vname[count($vname)-1];

								$vtype			=explode("/", $_FILES["files"]["type"][$row]);
								$type			=$vtype[0];
				
								if($type=="image")
								{	
									$data_im			=$this->__PROCESS_IMG($tempora);
									
									$im					=$data_im["im"];
									$width				=$data_im["width"];
									$height				=$data_im["height"];
									$orientation		=$data_im["orientation"];
								}
					

								$comando_sql	="INSERT INTO files (event_id, user_id, extension, temp, height, width,orientation)
								VALUES(	
									'$events_id', 
									'1', 
									'" . $extencion . "',
									'" . $temporal ."',									
									'" . $height . "',
									'" . $width . "',
									'" . $orientation . "'
								)";
								$file_id					=$this->__EXECUTE($comando_sql);					

								$archivo 					=$path . "file_" . md5($file_id) . ".";

								if($type=="image")			
								{
									// redimencionada
									$im->writeImage( $archivo . $extencion );	
									$th				=$im;

									// thumb
									$redimencion	=$this->__REDIMENSION(180, $width, $height);
									$width 			= $redimencion[1];
									$height 		= $redimencion[0];	
									$th->resizeImage($width,$height, imagick::FILTER_LANCZOS, 0.8, true);					

									$archivo 		=$path . "file_" . md5($file_id) . "_th." . $extencion;
									$th->writeImage( $archivo );
								}
								else if($type=="video")		
								{
									require 'nucleo/vendor/autoload.php';
								
									$ffmpeg = FFMpeg\FFMpeg::create();
									$video = $ffmpeg->open($temporal);

									$video
										->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(10))
										->save($archivo . "jpg");

									$data_im			=$this->__PROCESS_IMG($archivo . "jpg");
								
									$im					=$data_im["im"];
									$width				=$data_im["width"];
									$height				=$data_im["height"];
									$orientation		=$data_im["orientation"];

									$im->writeImage( $archivo . "jpg");	
									



									$video
										->filters()
										->resize(new FFMpeg\Coordinate\Dimension($width, $height))
										->synchronize();
									
									$video
										->save(new FFMpeg\Format\Video\WebM(), $archivo . "webm");
								}								
							}						
						}	
					}
				}				
			}
		}

		public function __FILES_COPI()
    	{    	
			#$this->__PRINT_R($this->__FILES_DATA);
			foreach($this->__FILES_DATA as $row=>$file)
			{
				$vtype			=explode("/", $file["type"]);
				$type			=$vtype[0];				
			}

		}		

		public function __PROCESS_IMG($temporal)
    	{    	
			$im 			= new imagick($temporal);
					
			$matrizExif = $im->getImageProperties("exif:*");

			$imageprops 	= $im->getImageGeometry();
			$width 			= $imageprops['width'];
			$height 		= $imageprops['height'];

			$redimencion	=$this->__REDIMENSION(700, $width, $height);

			$newWidth 			= $redimencion[1];
			$newHeight 		= $redimencion[0];	
			$im->resizeImage($newWidth,$newHeight, imagick::FILTER_LANCZOS, 0.8, true);					

			/*
			$logo = new Imagick();
			$logo->readImage("logo.png") or die("Couldn't load $logo");
			*/
			if(@$matrizExif["exif:Orientation"]==1)				$orientation 	= "horizontal";										
			if(@$matrizExif["exif:Orientation"]==6)
			{
				$width 			= $newHeight;
				$height 		= $newWidth;	

				$orientation 	= "vertical";
				//$logo->rotateimage(new ImagickPixel(), 270);
			}
			if(@$matrizExif["exif:Orientation"]==8)
			{
				$width 			= $newHeight;
				$height 		= $newWidth;	

				$orientation 	= "vertical";
				//$logo->rotateimage(new ImagickPixel(), 90);
			}

			if($orientation=="")
			{
				if($height>$width)	$orientation 	= "vertical";
				else				$orientation 	= "horizontal";
			}

			$return=array(
				"im"			=>$im,
				"width"			=>$width,
				"height"		=>$height,
				"orientation"	=>$orientation,
			);
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