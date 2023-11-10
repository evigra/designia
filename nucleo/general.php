<?php
	class general extends auxiliar
	{   
		##############################################################################	
		##  Metodos	
		##############################################################################		
		public function __CONSTRUCT()
		{	
			$files_available							=array("image/png","image/jpeg", "video/mp4");


			$this->words["server"]						=$_REQUEST["server"];
			$this->words["user"]						=$_REQUEST["user"];
			$this->words["path"]						=$_REQUEST["path"];
			
			$this->words["events_opciones"]				="";

			$this->__FILES_DATA							=array();	
			
			$this->__MENU_SESSION();
			if(isset($_FILES["files"]))
			{	
				if(isset($_REQUEST["event"]))
				{
					$comando_sql	="
						SELECT *, 
							e.id as event_id,
							f.id as file_id
						FROM 
							events e JOIN 
							files f ON e.id=f.event_id JOIN
							user u ON e.user_id=u.id 
						WHERE
							MD5(e.id)='{$_REQUEST["event"]}' 
					";					
				}
				else
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
				}

				
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
								$path="controlador/files/file/";
								
								if(!isset($events_id)) 
								{
									$events_id			=$this->__EXECUTE($comando_sql);
									if(isset($_REQUEST["event"]))
										$events_id=$events_id[0]["event_id"];
								}
								
								$newHeight 		= 0;
								$newWidth 		= 0;
								$orientation 	= "";

								$temporal		=$_FILES["files"]["tmp_name"][$row];
								$temporal_img	=$temporal;

								$vname			=explode(".", $_FILES["files"]["name"][$row]);
								$extencion		=$vname[count($vname)-1];
								$extencion_img	=$extencion;

								$vtype			=explode("/", $_FILES["files"]["type"][$row]);
								$type			=$vtype[0];

								if($type=="video")		
								{
									require 'nucleo/vendor/autoload.php';								
									$ffmpeg 			= FFMpeg\FFMpeg::create();
									$video 				= $ffmpeg->open($temporal);

									$temporal_img		=$temporal . "jpg";
									$extencion_img		="jpg";
									$video
										->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(10))
										->save($temporal_img );
								}
								
								$data_im			=$this->__PROCESS_IMG($temporal_img);							
								$im					=$data_im["im"];
								$width				=$data_im["width"];
								$height				=$data_im["height"];
								$orientation		=$data_im["orientation"];
					
								$comando_sql		="INSERT INTO files (event_id, user_id, extension, temp, height, width,orientation)
								VALUES(	
									'$events_id', 
									'1', 
									'" . $extencion . "',
									'" . $temporal ."',									
									'" . $height . "',
									'" . $width . "',
									'" . $orientation . "'
								)";
								$file_id			=$this->__EXECUTE($comando_sql);													
								$archivo 			=$path . "file_" . md5($file_id);
								if($type=="video")		
								{
									$video
										->filters()
										->resize(new FFMpeg\Coordinate\Dimension($width, $height))
										->synchronize();									
									$video
										->save(new FFMpeg\Format\Video\WebM(), $archivo.".webm");
								}
								
								// redimencionada
								$im->writeImage($archivo.".".$extencion_img );	
								$th				=$im;

								// thumb
								$redimencion	=$this->__REDIMENSION(180, $width, $height);								
								$height 		= $redimencion[0];	
								$width 			= $redimencion[1];								
								$th->resizeImage($width,$height, imagick::FILTER_LANCZOS, 0.8, true);					

								$th->writeImage($archivo."_th.".$extencion_img);
								
							}						
						}	
					}
				}				
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

			if(@$orientation=="")
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


		##############################################################################		 		
		public function __BROWSE()
    	{
			$this->words["html_cargar_title"]		="";
			$this->words["html_cargar_description"]	="";

			$files_image				=array("png","jpeg","jpg");
			$files_video				=array("mp4");

			$return			="";
			$user_ids		="";
			$usuarios		=array();

			$where			="";
			if($_REQUEST["class"]!="Chilaquil")
				$where			=" AND type='{$_REQUEST["class"]}'";

			$comando_sql	="
				SELECT *, 
					e.id as event_id 
				FROM 
					events e JOIN 
					user u ON e.user_id=u.id 				
				WHERE 1=1
					$where
				ORDER by e.id DESC
			";
			
			$events=$this->__EXECUTE($comando_sql);

			foreach($events as $id =>$row)
			{
				$comando_sql	="
					SELECT * FROM files
					WHERE event_id='" . $row["event_id"]. "'
					ORDER BY RAND()
					LIMIT 5
				";
				$files=$this->__EXECUTE($comando_sql);

				$title="";
				if($row["title"]!="")		
				{
					$title					="<h4>{$row["title"]}</h4>";

					$title_url				=str_replace(" ", "_", $row["title"]);   
					$title_url				=urlencode($title_url);
					$title_url				=str_replace("%", "_", $title_url);
					$title_url				=str_replace("/", "_", $title_url);					
				}
				else	$title_url			="Evento";

				$archivos					=count($files);

				if($archivos>2)				$archivos=3;
				else if($archivos==0)		$archivos=1;	

				$words_perfil				=$this->__PERFIL_DATA($row);

				$words_event=array(
					"events_perfil"			=>$this->__VIEW_BASE("perfil_header", $words_perfil),					
					"events_photos"			=>$this->__VIEW_BASE("galeria_fotos/galeria_fotos_" . random_int(1, $archivos), $words_perfil),															
					"events_title"			=>$title,
					"events_title_url"		=>$title_url,
					"events_description"	=>$row["description"],
				);				

				$paths=array();
				$rows=1;
				foreach($files as $file)
				{
					$path									="../../controlador/files/file/";
					$archivo 								=$path . "file_" . md5($file["id"]) . ".";

					$words_event["events_id"] 				=md5($row["event_id"]);					
					$words_event["file$rows"] 				=md5($file["id"]);	
					
					if(in_array($file["extension"], $files_image))
						$words_event["archivo".$rows]		="<img src=\"$archivo{$file["extension"]}\" width=\"100%\">";							
					if(in_array($file["extension"], $files_video))
						$words_event["archivo".$rows]		="<img class=\"video\" src=\"$archivo"."jpg\" width=\"100%\">";						
					$rows++;
				}

				$return	.=$this->__VIEW_BASE("contenido", $words_event);
			}
			return $return;
		}		##############################################################################		 		
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