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
					files f ON e.id=f.event_id JOIN
					user u ON e.user_id=u.id 
				WHERE
					MD5(e.id)='$_REQUEST[event]' 
					AND MD5(f.id)='$_REQUEST[file]'

			";
			$files=$this->__EXECUTE($comando_sql);
			if($files)
			{
				$return	="";	
				foreach($files as $id =>$row)
				{
					$path="../../modulos/files/file/";
					$md5_file=md5($row["file_id"]);
					$archivo =$path . "file_$md5_file." . $row["extension"];
					$words_perfil				=$this->__PERFIL_DATA($row);

					$words_template=array(
						"events_title"			=>$row["title"],
						"events_description"	=>$row["description"],
						"evento" 				=>$_REQUEST["event"],
						"index"					=>$id
					);		
					$return		.=$this->__VIEW_MODULE("fotos", $words_template);
					
					$words_file=array(						
						"foto$id" => $archivo,
						"file$id" => $md5_file
					);

					$return		=$this->__REPLACE($return,$words_file);
				}

				#$this->__PRINT_R($return);

				$words_event=array(
					"events_title"			=>$row["title"],
					"events_description"	=>$row["description"],
					"events_perfil"			=>$this->__VIEW_BASE("perfil_header", $words_perfil),					
					"events_photos"			=>$return,
				);

				$return	=$this->__VIEW_BASE("galeria", $words_event);
			}

			return $return;
		}
	}
?>
