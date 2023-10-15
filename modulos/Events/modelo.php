<?php
	class events extends general
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
			$comando_sql	="
				SELECT *, 
					e.id as event_id 
				FROM 
					events e JOIN 
					user u ON e.user_id=u.id 
				#WHERE e.id>10	
			";
			$events=$this->__EXECUTE($comando_sql);

			return $events;
		}
	}
?>
