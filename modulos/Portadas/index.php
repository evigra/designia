<?php
	$objeto			=new portadas();

	$objeto->words["html_head_css"]			="portada";
	$objeto->words["html_head_title"]		.="Portada";
	
	$objeto->words["html_head_description"]	="Esta descripcion";
	$objeto->words["html_head_keywords"]	="Designia, Designia.vip, Eventos, events";
	

	$objeto->words["html_body"]				=$objeto->__VIEW_BASE("body", $objeto->words);
	
	$objeto->words["html_left"]				="";
	$objeto->words["html_center"]			=$objeto->__HTML_CENTER();
	$objeto->words["html_right"]			="";

	#$objeto->words["html_right"]			=$objeto->__HTML_CENTER();

	echo $objeto->__VIEW_BASE("index", $objeto->words);	
?>
