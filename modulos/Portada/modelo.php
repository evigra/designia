<?php
	class portada extends general
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
   		public function __HTML_CENTER()
    	{
			$return			="";
			$user_ids		="";
			$usuarios		=array();

			$comando_sql	="
				SELECT *, 
					e.id as event_id 
				FROM 
					events e JOIN 
					user u ON e.user_id=u.id JOIN
					files f on e.id=f.event_id AND extension in('png','jpg', 'jpeg')
				#WHERE type='" . $_REQUEST["class"]. "'
				ORDER by e.id DESC
				LIMIT 1
			";
			
			$events=$this->__EXECUTE($comando_sql);
			foreach($events as $event)
			{
				$comando_sql	="
					SELECT * FROM files
					WHERE event_id='" . $event["event_id"]. "'
					ORDER BY RAND()
				";
				$files=$this->__EXECUTE($comando_sql);
				foreach($files as $id =>$row)
				{
					$path="../../modulos/files/file/";
					$md5_file=md5($row["id"]);
					$archivo =$path . "file_$md5_file." . $row["extension"];
		
					$words_event=array(
						"index"					=>$id,
					);
					$return	.=$this->__VIEW_MODULE("fotos", $words_event);
					
					$words_file=array(						
						"foto$id" => $archivo,
						"style$id"=> $row["orientation"],
						"file$id" => $md5_file
					);
					$return		=$this->__REPLACE($return,$words_file);				
				}
			}
			return $return;
		}
	}
?>
