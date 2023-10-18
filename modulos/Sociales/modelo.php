<?php
	class sociales extends general
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
				WHERE type='" . $_REQUEST["class"]. "'
				ORDER by e.id DESC
			";
			
			$events=$this->__EXECUTE($comando_sql);

			foreach($events as $id =>$row)
			{
				$comando_sql	="
					SELECT * FROM files
					WHERE event_id='" . $row["event_id"]. "'
					ORDER BY RAND(), orientation desc
					LIMIT 5
				";
				$files=$this->__EXECUTE($comando_sql);

				if($row["title"]!="")		$row["title"]		="<h4>{$row["title"]}</h4>";
				if($row["description"]!="")	$row["description"]	="<span>{$row["description"]}</span>";

				$archivos		=count($files);

				if($archivos>2)	$archivos=3;
				else if($archivos==0)	$archivos=1;	

				$words_perfil				=$this->__PERFIL_DATA($row);
				$words_event=array(
					"events_perfil"			=>$this->__VIEW_BASE("perfil_header", $words_perfil),					
					"events_photos"			=>$this->__VIEW_BASE("galeria_fotos/galeria_fotos_" . random_int(1, $archivos), $words_perfil),					
					"events_title"			=>$row["title"],
					"events_description"	=>$row["description"],
				);				

				$paths=array();
				$rows=1;
				foreach($files as $file)
				{
					$path="../../modulos/files/file/";
					$archivo =$path . "file_" . md5($file["id"]) . "." . $file["extension"];

					$words_event["events_id"] 	=md5($row["event_id"]);					
					$words_event["file$rows"] 	=md5($file["id"]);					


					if(in_array($file["extension"], $files_image))
					{
						$words_event["archivo".$rows]="<img src=\"$archivo\" width=\"100%\">";							
					}
					if(in_array($file["extension"], $files_video))
					{
						$words_event["archivo".$rows]="
						<video  style=\"max-height:600px; max-width:800px;\"  controls>
							<source src=\"$archivo\" type=\"video/mp4\">
							Your browser does not support the video tag.
						</video> 												  				
					  	";					
					}
					$rows++;
				}

				$return	.=$this->__VIEW_BASE("contenido", $words_event);
			}
			return $return;
		}
	}
?>
