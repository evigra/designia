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
			";
			
			$events=$this->__EXECUTE($comando_sql);

			foreach($events as $id =>$row)
			{

				$words_perfil				=$this->__PERFIL_DATA($row);
				$words_event=array(
					"events_id"				=>md5($row["event_id"]),
					"events_perfil"			=>$this->__VIEW_BASE("perfil_header", $words_perfil),					
					"events_photos"			=>$this->__VIEW_BASE("galeria_fotos/galeria_fotos_" . random_int(1, 3), $words_perfil),					
					"events_title"			=>$row["title"],
					"events_description"	=>$row["description"],
				);				

				$comando_sql	="
					SELECT * FROM files
					WHERE event_id='" . $row["event_id"]. "'
					ORDER BY RAND()
					LIMIT 5
				";
				$files=$this->__EXECUTE($comando_sql);

				$paths=array();
				$rows=1;
				foreach($files as $file)
				{
					$path="../../modulos/files/file/";
					$archivo =$path . "file_" . md5($file["id"]) . "." . $file["extension"];
					$words_event["foto".$rows]=$archivo;
					$rows++;
				}


				
				$return	.=$this->__VIEW_BASE("contenido", $words_event);
			}
			return $return;
		}
	}
?>
