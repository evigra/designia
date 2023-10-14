<?php
	class galerias extends general
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
			$return			="";
			$user_ids		="";
			$usuarios		=array();

			$comando_sql	="
				SELECT *, 
					e.id as event_id 
				FROM 
					events e JOIN 
					user u ON e.user_id=u.id 
				WHERE type='galeria'
			";
			$events=$this->__EXECUTE($comando_sql);

			foreach($events as $id =>$row)
			{
				$words_perfil=array(
					"perfil_name"	=>$row["name"],
					"perfil_user"	=>$row["user"],
					"perfil_date"	=>$row["datetime_show"],
					"perfil_type"	=>$row["type"],					
					"perfil_url"	=>"http://". $row["user"].".".$_REQUEST["server"],			
				);
				$words_event=array(
					"events_id"				=>md5($row["event_id"]),
					"events_perfil"			=>$this->__VIEW_BASE("perfil_header", $words_perfil),					
					"events_title"			=>$row["title"],
					"events_description"	=>$row["description"],
				);				
				$return	.=$this->__VIEW_BASE("contenido", $words_event);
			}
			return $return;
		}
	}
?>
