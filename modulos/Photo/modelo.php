<?php
	class event extends general
	{   
		##############################################################################	
		##  Propiedades	
		##############################################################################

		##############################################################################	
		##  Metodos	
		##############################################################################
         
		public function __CONSTRUCT($option=null)
		{	
			return parent::__CONSTRUCT($option);
		}
   		public function __BROWSE()
    	{
			$files_image				=array("png","jpeg","jpg");
			$files_video				=array("mp4");

			$words_event=array(
				"events_title"			=>"Este es el titulo del evento",
				"events_description"	=>"Aqui apareceria la descipcion del evento",
				"events_photos"			=>"ESTAS INGREADO UNA URL QUE NO EXISTE",
			);
			$return	=$this->__VIEW_BASE("galeria", $words_event);
			$user_ids		="";
			$usuarios		=array();

			$comando_sql	="
				SELECT *, 
					e.id as event_id,
					f.id as file_id
				FROM 
					events e JOIN 
					files f ON e.id=f.event_id JOIN
					user u ON e.user_id=u.id 
				WHERE
					MD5(e.id)='$_REQUEST[event]' 
					#AND MD5(f.id)='$_REQUEST[file]'

			";
			$files=$this->__EXECUTE($comando_sql);
			
			if($files)
			{
				$return	="";	
				foreach($files as $id =>$file)
				{
					$path="../../modulos/files/file/";
					$md5_file=md5($file["file_id"]);

					$archivo 	="";

					if($_REQUEST["file"]==$md5_file)
					{
						$archivo 	=$path . "file_$md5_file.";

						$photo	="<img src=\"$archivo{$file["extension"]}\" width=\"100%\">";

						if(in_array($file["extension"], $files_image))
						{
							$photo="<img src=\"$archivo{$file["extension"]}\" width=\"100%\">";							
						}
						if(in_array($file["extension"], $files_video))
						{
							$photo="
								<video style=\"max-height:600px; max-width:800px; width:100%;\" autoplay controls>
									<source src=\"$archivo"."webm\" type=\"video/webm\">
									Your browser does not support the video tag.
								</video> 
							";
						}
					}		
					
					$archivo 	=$path . "file_$md5_file";
					

					if(in_array($file["extension"], $files_image))
					{
						$archivo	="<img src=\"$archivo"."_th.{$file["extension"]}\">";	
					}
					if(in_array($file["extension"], $files_video))
					{
						$archivo	="<img src=\"$archivo"."_th.jpg\">";	

					}

					$words_perfil				=$this->__PERFIL_DATA($file);

					$title="";
					if($file["title"]!="")		
					{
						$title					="<h4>{$file["title"]}</h4>";
		
						$title_url				=str_replace(" ", "_", $file["title"]);   
						$title_url				=urlencode($title_url);
						$title_url				=str_replace("%", "_", $title_url);
						$title_url				=str_replace("/", "_", $title_url);					
					}
					else	$title_url			="Evento";
		
					$words_template=array(
						#"events_title"			=>$file["title"],
						#"events_description"	=>$file["description"],
						"evento" 				=>$_REQUEST["event"],
						"index"					=>$id,
						"events_title"			=>$title,
						"events_title_url"		=>$title_url,
					);		

					$return		.=$this->__VIEW_MODULE("fotos", $words_template);
					
					$words_file=array(						
						"foto$id" => $archivo,
						"file$id" => $md5_file
					);
					$return		=$this->__REPLACE($return,$words_file);
				}

				$words_event=array(
					"events_title"			=>$file["title"],
					"events_description"	=>$file["description"],
					"events_perfil"			=>$this->__VIEW_BASE("perfil_header", $words_perfil),					
					"events_photo"			=>$photo,	
					"events_photos"			=>$return,
				);

				$return	=$this->__VIEW_BASE("galeria", $words_event);
			}
			return $return;
		}
	}
?>
