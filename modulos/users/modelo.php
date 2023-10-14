<?php
	class users extends general
	{   
		##############################################################################	
		##  Propiedades	
		##############################################################################
		var $mod_menu=array();
		var $sys_fields		=array( 
			"id"	    =>array(
			    "title"             => "id",			    
			    "type"              => "primary key",
			),
			/*
			"trabajador_id"	    =>array(
			    "title"             => "Trabajador",
			    #"description"       => "Responsable del dispositivo",
			    "type"              => "autocomplete",
			    "procedure"       	=> "__AUTOCOMPLETE",
			    #"relation"          => "one2many",
			    "relation"          => "many2one",
			    "class_name"       	=> "trabajador",
			    "class_field_l"    	=> "nombre",				# Label
			    "class_field_o"    	=> "trabajador_id",
			    "class_field_m"    	=> "id",			    
			),
			#*/			
			"name"	    =>array(
			    "title"             => "Nombre",
			    "title_filter"      => "Nombre",
			    "type"              => "input",
			),

			"email"	    =>array(
			    "title"             => "Mail",
			    "title_filter"      => "Mail",
			    "type"              => "input",
			),
			"password"	    =>array(
			    "title"             => "Password",
			    "type"              => "password",
			),			
			"hashedPassword"	    =>array(
			    "title"             => "Password",
			    "type"              => "password",
			),
			"departamento_id"	    =>array(
			    "title"             => "ID Departamento",
			    "type"              => "input",
			),

			/*
			"files_id"	    =>array(
			    "title"             => "Imagen",
			    "type"              => "file",
			    #"relation"          => "one2many",
			    "relation"          => "many2one",
			    "class_name"       	=> "files",
			    "class_field_o"    	=> "files_id",
			    "class_field_m"    	=> "id",			    
			),
			"img_files_id"	    =>array(
			    "title"             => "Imagen",
			    "type"              => "file",
			),
			*/	
			#/*		
			"sesion_start"	    =>array(
			    "title"             => "Modulo de inicio",
			    "type"              => "autocomplete",
			    "procedure"       	=> "autocomplete_modulos",
			    "relation"          => "many2one",
			    "class_name"       	=> "modulo",
			    "class_field_l"    	=> "name",				# Label
			    "class_field_o"    	=> "sesion_start",
			    "class_field_m"    	=> "menu",			    
			),
			"menu"	    =>array(
			    "class_name"          => "menu",
			),

			#*/
			/*								
			"company_id"	    =>array(
			    "title"             => "Compania",
			    "type"              => "input",
			    #"relation"          => "one2many",
			    "relation"          => "many2one",
			    "class_name"       	=> "company",
			    "class_field_o"    	=> "company_id",
			    "class_field_m"    	=> "id",
			),
			*/						
			"salt"	    		=>array(
			    "title"             => "Compania",
			    "type"              => "input",
			),
			
			"usergroup_ids"	    	=>array(
			    "title"             => "Permisos",
			    "type"              => "input",
			    #"relation"          => "one2many",
			    "relation"          => "many2one",
			    "class_name"       	=> "user_group",
			    "class_field_o"    	=> "id",
			    "class_field_m"    	=> "user_id",
			),
			/*
			"devices_ids"	    	=>array(
			    "title"             => "Permisos",
			    "type"              => "input",
			    "relation"          => "one2many",			    
			    "class_name"       	=> "devices",
			    #"class_path"        => "modulos/devices/modelo.php",
			    #"class_field_o"    	=> "id",
			    #"class_field_m"    	=> "responsable_fisico_id",
			    #"value"             => "",			    
			),
			*/
			"status"	    =>array(
			    "title"             => "Activo",
			    "type"              => "checkbox",
			),				
		);				
		##############################################################################	
		##  Metodos	
		##############################################################################

        
		public function __CONSTRUCT($option=null)
		{	
			/*
			$option		=array(		
				"name"			=>"files_obj",		
				"memory"		=>"files_obj",
			);			
			$this->files_obj		=new files($option);
			*/
			
			/*
			$option_menu		=array(	
				"name"			=>"sys_fields["menu"]["obj"]",		
				"memory"		=>"sys_fields["menu"]["obj"]",
			);						
			$this->sys_fields["menu"]["obj"]			=new menu($option_menu);
			*/
			#$this->device_obj		=new device();
			#$this->usergroup_obj	=new user_group();

			return parent::__CONSTRUCT($option);
		}
   		public function __SAVE($datas=NULL,$option=NULL)
    	{
    		## GUARDAR USUARIO
    		if(count($datas)>2)
    		{
			    $datas["company_id"]    	=$_SESSION["company"]["id"];
			    $datas["hashedPassword"]	="ef38a22ac8e75f7f3a6212dbfe05273365333ef53e34c14c";
			    $datas["salt"]				="000000000000000000000000000000000000000000000000";
			    if(isset($datas["password"]) AND $datas["password"]!="")
				    $datas["password"]		=md5($datas["password"]);
				else
					unset($datas["password"]);    
			    /*
			    $files_id					=$this->obj_files_id->__SAVE();    	    
			    if(!is_null($files_id))		$datas["files_id"]			=$files_id;    	    
				*/
			    $user_id=parent::__SAVE($datas,$option);
			    
			    			    
			    ## GUARDAR PERFILES DE USUARIO
			    $usergroup_datas=array();
			    if(isset($datas["usergroup_ids"]))
			    {
					foreach($datas["usergroup_ids"] as $index => $data)
					{
						$usergroup_option=array();
						## BUSCA PERFIL PREVIO 
						## SI EXISTE, LO MODIFICA
						## SI NO, LO CREA
						$usergroup_option["where"]=array(
							"user_id=$user_id",
							"company_id={$_SESSION["company"]["id"]}",
							"menu_id={$index}",
						);    	    		    	    		
						$usergroup_data						=$this->sys_fields["usergroup_ids"]["obj"]->groups($usergroup_option);

						if($usergroup_data["total"]>0)		$this->sys_fields["usergroup_ids"]["obj"]->sys_private["id"]=$usergroup_data["data"][0]["id"];
						else								$this->sys_fields["usergroup_ids"]["obj"]->sys_private["id"]=NULL;

						$usergroup_save=array(
							"user_id"		=>"$user_id",
							"company_id"	=>"{$_SESSION["company"]["id"]}",
							"menu_id"		=>"{$index}",
							"active"		=>"$data"
						);	
						$this->sys_fields["usergroup_ids"]["obj"]->__SAVE($usergroup_save);
					}	
				}    
			}	
		}		
		#/*
		public function __FIND_FIELDS($id=NULL)
		{
			parent::__FIND_FIELDS($id);
			if(@$this->sys_private["section"]=="write")
				$this->sys_fields["password"]["value"]="";			
    	}
    	#*/
		public function __INPUT($words=NULL,$sys_fields=NULL)
		{	
			$this->words					=parent::__INPUT($words,$sys_fields);
			
			$this->words["permisos"]	    =$this->sys_fields["menu"]["obj"]->grupos_html(@$this->sys_fields["usergroup_ids"]["values"]);
			
			/*			
			if($this->sys_fields["files_id"]["value"]>0)
			{				
				if(isset($this->sys_fields["files_id"]["value"]))    	
					$this->words["img_files_id"]	            =$this->sys_fields["files_id"]["obj"]->__GET_FILE($this->sys_fields["files_id"]["value"]);
				else	$this->words["img_files_id"]="";	
			}
			*/			
			return $this->words;
    	}

		public function session($user,$pass)
    	{
    	    $option=array(
    	    	"where"=>
			    	array(
						"email='$user'",
						"password=md5('$pass')",
						"status=1"
			    	),
    	    );
    	    #$option["echo"]="USERS sesion()";
    	    $data_user	=$this->users($option);    	        	    
    	    if(is_array($data_user) AND array_key_exists("data",$data_user))
    	    {    	    	
    	    	if(count($data_user["data"])>0)	$return=$data_user["data"][0];
    	    	else							$return=$data_user["data"];
    	    }
			return $return;
		}		
		//////////////////////////////////////////////////		
		public function autocomplete_user()		
    	{	
    		$option					=array();
    		$option["where"]		=array();    		
    		$option["where"][]		="name LIKE '%{$_GET["term"]}%'";
    		
			$return =$this->__BROWSE($option);    				
			return $return;			
		}				
    	//////////////////////////////////////////////////	
	
		public function users($option=NULL)		
    	{	
			$return =$this->__VIEW_REPORT($option);    				
			return $return;
		}				
		public function __BROWSE($option=NULL)		
    	{	
    		if(is_null($option))			$option					=array();
    		if(!isset($option))				$option					=array();
    		
    		if(!isset($option["select"]))	$option["select"]		=array();
    		if(!isset($option["where"]))	$option["where"]		=array();
    		
			#$option["select"]["admin_soles37.FN_ImgFile('../modulos/users/img/user.png',files_id,0,0)"]		="img_files_id";
			#$option["select"]["admin_soles37.FN_ImgFile('../modulos/users/img/user.png',files_id,300,300)"]	="img_files_id_med";				
			##$option["select"]["admin_soles37.FN_ImgFile('../modulos/users/img/user.png',files_id,150,90)"]	="img_files_id_chi";
			#$option["select"]["admin_soles37.FN_ImgFile('../modulos/users/img/user.png',files_id,40,24)"]	="img_files_id_sup_chi";
    		
    		
			#$option["select"]["FN_ImgFile('../modulos/users/img/user.png',files_id,0,0)"]	="img_files_id";
            #$option["select"]["FN_ImgFile('../modulos/users/img/user.png',files_id,0,30)"]	="img_files_id_min";
            #$option["select"]["FN_ImgFile('../modulos/users/img/user.png',files_id,0,150)"]	="img_files_id_med";
			$option["select"][]																="u.*";
			$option["from"]		="users u";			

			#if(isset($_SESSION["company"]["id"]) AND isset($_SESSION["user"]["id"]))
			#	$option["where"][]	="(u.company_id={$_SESSION["company"]["id"]} or u.id={$_SESSION["user"]["id"]})";									    				

			return parent::__BROWSE($option);
		}				

	}
?>
