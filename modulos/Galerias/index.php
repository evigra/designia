<?php
	$objeto			=new galerias();

	$objeto->words["html_head_title"]		.=$_REQUEST["class"];
	$objeto->words["html_head_description"]	="Esta descripcion";
	$objeto->words["html_head_keywords"]	="Designia, Designia.vip, Eventos, events";
	

	$objeto->words["html_body"]				=$objeto->__VIEW_BASE("body", $objeto->words);
	$objeto->words["html_center"]			=$objeto->__BROWSE();
	#$objeto->words["system_body"]	=	$objeto->__TEMPLATE($objeto->sys_html."system_body");	

	echo $objeto->__VIEW_BASE("index", $objeto->words);	
?>
