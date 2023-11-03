<?php
	class chilaquil extends general
	{   
		##############################################################################	
		##  Propiedades	
		##############################################################################

		##############################################################################	
		##  Metodos	
		##############################################################################
        
		public function __CONSTRUCT($option=null)
		{	
			parent::__CONSTRUCT($option);
		}
		public function __BROWSE()
    	{
			$files_image				=array("png","jpeg","jpg");
			$files_video				=array("mp4");

			$return			="";
			$user_ids		="";
			$usuarios		=array();

			$comando_sql	="
				SELECT *, 
					e.id as event_id 
				FROM 
					events e JOIN 
					user u ON e.user_id=u.id 				
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

				if($row["description"]!="")	$row["description"]	="<span>{$row["description"]}</span>";

				$archivos					=count($files);

				if($archivos>2)	$archivos=3;
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
					$path="../../modulos/files/file/";
					$archivo =$path . "file_" . md5($file["id"]) . ".";

					$words_event["events_id"] 	=md5($row["event_id"]);					
					$words_event["file$rows"] 	=md5($file["id"]);	
					
					if(in_array($file["extension"], $files_image))
					{
						$words_event["archivo".$rows]="<img src=\"$archivo{$file["extension"]}\" width=\"100%\">";							
					}
					if(in_array($file["extension"], $files_video))
					{
						$words_event["archivo".$rows]="<img src=\"$archivo"."jpg\" width=\"100%\">";						
						/*	
						$words_event["archivo".$rows]="
							
							<div style=\"width:100px; heigth:100px; background-color:red;\">aaa
								<video  style=\"max-height:600px; max-width:800px;\"  controls>
									<source src=\"$archivo\" type=\"video/mp4\">
									Your browser does not support the video tag.
								</video> 												  	
							</div>
						";
						*/
					}
					$rows++;
				}

				$return	.=$this->__VIEW_BASE("contenido", $words_event);
			}
			return $return;
		}
	}
?>
