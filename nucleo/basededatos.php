<?php	
	class basededatos 
	{    
		public function __SYS_DB()
		{  
			if($_REQUEST["server"]=="designia.localhost")
			{
				$host	="localhost";
				$db		="produccion";
			}
			if($_REQUEST["server"]=="designia.vip")
			{
				if($this->words["user"]=="developer")
				{
					$host	="localhost";
					$db		="developer";
				}
				if($this->words["user"]!="developer")
				{
					$host	="localhost";
					$db		="produccion";
				}
			}			

			return array(
				"user"		=>"remoto",
				"pass"		=>"EvG30JiC06",
				"name"		=>$db,
				"host"		=>$host,
				"type"		=>"mysql",
			);
		}


		
		function abrir_conexion()
		{
			$OPHP_database=$this->__SYS_DB();
			if($OPHP_database["type"]=="mysql")	        	
			{			
				$this->OPHP_conexion = @mysqli_connect($OPHP_database["host"], $OPHP_database["user"], $OPHP_database["pass"], $OPHP_database["name"]) OR $this->reconexion();				
			}
		}

		function reconexion()
		{
			$OPHP_database=$this->__SYS_DB();
			if($OPHP_database["type"]=="mysql")	        	
			{
				$this->OPHP_conexion = @mysqli_connect("localhost", $OPHP_database["user"], $OPHP_database["pass"], $OPHP_database["name"]);
			}
		}
		
		function cerrar_conexion()
		{
			if(isset($this->OPHP_conexion) AND is_object($this->OPHP_conexion))
		    	@$this->OPHP_conexion->close();
		    else
		    {
		    	echo "SE PRESENTO UNA FALLA EN LA CONECCION";	
		    	exit();
		    }	
		}	

		public function __PRINT_R($variable, $title=NULL)
		{  
		    echo "<div class=\"developer\" title=\"Sistema $title\"><pre>";
		    @print_r(@$variable);
		    echo "</pre></div>";		    			
    	} 
	}
?>