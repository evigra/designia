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
					e.id as event_id 
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
					$words_files=array(
						"events_title"			=>$row["title"],
						"events_description"	=>$row["description"],
					);
									
					$return	.=$this->__VIEW_BASE("fotos", $words_files);
				}
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
