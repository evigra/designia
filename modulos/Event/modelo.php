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
					files f ON e.id=f.event_id 
				WHERE
					MD5(e.id)='$_REQUEST[id]'

			";
			$files=$this->__EXECUTE($comando_sql);
			if($files)
			{
				$return	="";	
				foreach($files as $id =>$row)
				{
					$path="../../modulos/files/file/";
					$archivo =$path . "file_" . md5($row["file_id"]) . "." . $row["extension"];

					$words_files=array(
						"events_title"			=>$row["title"],
						"events_description"	=>$row["description"],
						"index"					=>$id
					);		
					$return		.=$this->__VIEW_MODULE("fotos", $words_files);
					$return		=$this->__REPLACE($return,array("foto$id" => $archivo));
				}

				#$this->__PRINT_R($return);

				$words_event=array(
					"events_title"			=>$row["title"],
					"events_description"	=>$row["description"],
					"events_photos"			=>$return,
				);

				$return	=$this->__VIEW_BASE("galeria", $words_event);
			}

			return $return;
		}
	}
?>
