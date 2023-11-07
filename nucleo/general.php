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

								if(!isset($events_id)) $events_id			=$this->__EXECUTE($comando_sql);

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

		public function __FILES_COPI()
    	{    	
			#$this->__PRINT_R($this->__FILES_DATA);
			foreach($this->__FILES_DATA as $row=>$file)
			{
				$vtype			=explode("/", $file["type"]);
				$type			=$vtype[0];				
			}

		}		

		public function __SOCIAL_NETWORKS($url, $file_id)
    	{    	

			$url=rawurlencode("http://" . $_SERVER["SERVER_NAME"] . "/&abrev=$file_id");
						
			$return="";
/*
					<div style=\"height:50px; width:150px; font-size:30px;\" class=\"fb-share-button\" data-href=\"http://{$this->words["html_head_url"]}\" data-layout=\"\" data-size=\"\">
					<a target=\"_blank\" href=\"https://www.facebook.com/sharer/sharer.php?u=http://{$this->words["html_head_url"]}%2F&amp;src=sdkpreparse\" class=\"fb-xfbml-parse-ignore\">Compartir</a><
				/div>
	https://www.pinterest.com/pin/create/button/?url=https%3A//youtube.com/watch%3Fv%3DKrLj6nc516A%26si%3DPZ3DGebdG9ZHwI6T&description=Auto%20de%20%241%20Vs%20Auto%20de%20%24100%2C000%2C000&is_video=true&media=https%3A//i.ytimg.com/vi/KrLj6nc516A/maxresdefault.jpg
	https://www.reddit.com/submit?url=https%3A%2F%2Fyoutube.com%2Fwatch%3Fv%3DKrLj6nc516A%26si%3DPZ3DGebdG9ZHwI6T&title=Auto%20de%20%241%20Vs%20Auto%20de%20%24100%2C000%2C000

	https://api.whatsapp.com/send/?text=https%3A%2F%2Fwww.facebook.com%2FLEBRAYAN%2Fposts%2Fpfbid03f8L2jiWnng22gwrkmFccKPaqzYZ9jQf6p6r2CpTnFc6JEg92eM6qoCQLPYeUZEyl%3Fmibextid%3DbKks23&type=custom_url&app_absent=0		
	https://l.facebook.com/l.php?u=https%3A%2F%2Fwa.me%2F%3Ftext%3Dhttps%253A%252F%252Fwww.facebook.com%252FLEBRAYAN%252Fposts%252Fpfbid03f8L2jiWnng22gwrkmFccKPaqzYZ9jQf6p6r2CpTnFc6JEg92eM6qoCQLPYeUZEyl%253Fmibextid%253DbKks23%26fbclid%3DIwAR0VJ-Ba8pwwOMw3P-URxkxdkTHBRuWV2BcKWeB5XLk0wnzCGF58HXw7ru8&h=AT2QVyMXG13krpus2qKbsavXI59QYbG6kj05XrR9fwx_13Hz15lt68lPux678xtT59yssrxC7iLfW3Z4TV7Lsnvcy9ue6sFSoVk229z9v8qtyZRYlkI-471HVhwRU8WwEYcFigG2MV0BMg&__tn__=J]-R&c[0]=AT3fi1NU7v_dksXme7pN5fE4QZQ9AlHT-ydLLa0Outpt_aCBLA9BmTvbjoGwGsD-aeXpQVWAhXKzrmBRaqrsGWm3tArpoupuflgu9yR4wSngqt22IJmUM3ksbOtT9HX05oLNJAdUz97MTnhZQ5H346bPcUQCAdKml-Cp3BaJ1sSu1q9CIc7O6WzFB5PhaA2VXej9A7TziXU
	https://wa.me/?text=https%3A%2F%2Fwww.facebook.com%2FLEBRAYAN%2Fposts%2Fpfbid03f8L2jiWnng22gwrkmFccKPaqzYZ9jQf6p6r2CpTnFc6JEg92eM6qoCQLPYeUZEyl%3Fmibextid%3DbKks23&type=custom_url&app_absent=0		

	https://twitter.com/intent/tweet?url=https%3A//youtu.be/LeYsRMZFUq0%3Fsi%3De2Mb9Lb4e7cMgFKw&text=Les%20Doy%20%241%2C000%2C000%20con%20Solo%201%20Minuto%20para%20Gastarlo&via=YouTube&related=YouTube,YouTubeTrends,YTCreators

	https://www.facebook.com/dialog/share?
  app_id=145634995501895
  &display=popup
  &href=https%3A%2F%2Fdevelopers.facebook.com%2Fdocs%2F
  &redirect_uri=https%3A%2F%2Fdevelopers.facebook.com%2Ftools%2Fexplorer
	*/


			$return.="<a href=\"https://www.facebook.com/dialog/share?
			app_id=1984430411957885
			&display=popup
			&href=$url"."&type=custom_url&app_absent=0\" target=\"_blank\">
				<img width=\"40\" src=\"../../sitio_web/img/facebook.svg\">";

			$return.="<a href=\"https://twitter.com/intent/tweet?url=$url\">
				<img width=\"40\" src=\"../../sitio_web/img/twiter.jpg\">";

			$return.="<a href=\"https://api.whatsapp.com/send/?text=$url\">
				<img width=\"45\" src=\"../../sitio_web/img/WhatsApp.png\">";		

			$return.="<a href=\"https://www.pinterest.com/pin/create/button/?url=$url"."&type=custom_url&app_absent=0\">
				<img width=\"40\" src=\"../../sitio_web/img/pinterest.png\">";

			$return.="<a href=\"https://www.reddit.com/submit?url=$url"."&type=custom_url&app_absent=0\">
				<img width=\"40\" src=\"../../sitio_web/img/reddit.png\">";

			$return.="<a class=\"acortador\" title=\"http://" . $_SERVER["SERVER_NAME"] . "/&abrev=$file_id\">
				<img width=\"40\" src=\"../../sitio_web/img/acortador.jpg\">";
					
			return $return; 
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