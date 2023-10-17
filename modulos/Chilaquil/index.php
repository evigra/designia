<?php
	$objeto			=new chilaquil();

	$objeto->words["html_head_css"]			="default";
	$objeto->words["html_head_title"]		.="Chilaquil";
	
	$objeto->words["html_head_description"]	="Esta descripcion";
	$objeto->words["html_head_keywords"]	="Designia, Designia.vip, Eventos, events";
	
	$objeto->words["html_body"]				=$objeto->__VIEW_BASE("body", $objeto->words);

	$objeto->words["html_left"]				="";
	$objeto->words["html_center"]			=$objeto->__BROWSE();
	$objeto->words["html_right"]			="";

	$objeto->words["html_menu"]				=$objeto->__VIEW_BASE("menu", $objeto->words);
	$objeto->words["html_pie"]				=$objeto->__VIEW_BASE("pie", $objeto->words);
	
	echo $objeto->__VIEW_BASE("index", $objeto->words);	
?>




<?php
/*
	$objeto			=new events();

	$objeto->words["html_head_css"]			="default";
	$objeto->words["html_head_title"]		.="Chilaquil";
	
	$objeto->words["html_head_description"]	="Esta descripcion";
	$objeto->words["html_head_keywords"]	="Designia, Designia.vip, Eventos, events";
	

	$objeto->words["html_body"]				=$objeto->__VIEW_BASE("body", $objeto->words);
	$events_data							=$objeto->__BROWSE();

	$return			="";
	$user_ids		="";
	$usuarios		=array();


	foreach($events_data as $id =>$row)
	{
		$words_perfil=array(
			"perfil_name"	=>$row["name"],
			"perfil_user"	=>$row["user"],
			"perfil_date"	=>$row["datetime_show"],
			"perfil_type"	=>$row["type"],					
			"perfil_url"	=>"http://". $row["user"].".".$_REQUEST["server"],			
		);
		$words_fotos=array(
			"photo1"	=>$row["name"],
			"photo2"	=>$row["user"],
			"photo3"	=>$row["datetime_show"],
		);
		$words_event=array(
			"events_id"				=>md5($row["event_id"]),
			"events_perfil"			=>$objeto->__VIEW_BASE("perfil_header", $words_perfil),					
			"events_photos"			=>$objeto->__VIEW_BASE("galeria_fotos_" . random_int(1, 3), $words_perfil),					
			"events_title"			=>$row["title"],
			"events_description"	=>$row["description"],
			"servidor"				=>$_REQUEST["server"],
			"user"				=>$_REQUEST["user"],
		);				
		$return	.=$objeto->__VIEW_BASE("contenido", $words_event);
	}

	$objeto->words["html_center"]			=$return;

	$objeto->words["html_right1"]			="";
	$objeto->words["html_right2"]			="";
	$objeto->words["html_right3"]			="";

	$objeto->words["html_left1"]			=$objeto->__VIEW_BASE("iniciar_sesion", $words_perfil);	
	$objeto->words["html_left2"]			="";
	$objeto->words["html_left3"]			="";
	
	echo $objeto->__VIEW_BASE("index", $objeto->words);	
*/
?>
