<?php
	#require_once("modelo.php");

	$objeto										=new files();
	$objeto->__SESSION();
	#$objeto->__PRINT_R($objeto);
	
	

	$objeto->words["system_body"]               =$objeto->__TEMPLATE($objeto->sys_html."system_body"); 			# TEMPLATES ELEJIDOS PARA EL MODULO
	$objeto->words["system_module"]             =$objeto->__TEMPLATE($objeto->sys_html."system_module");
	
	
	$objeto->words["html_head_js"]              =$objeto->__FILE_JS(array("../".$objeto->sys_module."js/index"));
	$objeto->words["html_head_css"]              =$objeto->__FILE_CSS(array("../sitio_web/css/basicItems"));
	
	#$objeto->sys_section="kanban";

    $module_left=array(
        array("action"=>"Guardar"),
        array("cancel"=>"Cancelar"),
    );
    $module_right=array(
        array("create"=>"Crear"),
        #array("write"=>"Modificar"),
        array("kanban"=>"Kanban"),
        array("report"=>"Reporte"),
    );
    $module_center                                  ="";
    $module_title                                   ="";

    if($objeto->sys_section=="create")
	{
    	$objeto->words["module_body"]               =$objeto->__VIEW_CREATE($objeto->sys_module . "html/create");	
    	$objeto->words                              =$objeto->__INPUT($objeto->words,$objeto->sys_fields);    
    }	
    elseif($objeto->sys_section=="write")
	{
    	$objeto->words["module_body"]               =$objeto->__VIEW_WRITE($objeto->sys_module . "html/write");	
    	$objeto->words                              =$objeto->__INPUT($objeto->words,$objeto->sys_fields);
    }	
	elseif($objeto->sys_section=="kanban")
	{
	    $module_left                                ="";
		$template_body								=$objeto->sys_module . "html/kanban";	
	   	$data										=$objeto->devices();        	
    	$objeto->words["module_body"]               =$objeto->__VIEW_KANBAN($template_body,$data["data"]);	
    }	
	elseif($objeto->sys_section=="report")
	{
	    $module_left                                ="";
		$option                                     =array();

		$option["template_title"]	                = $objeto->sys_module . "html/report_title";
		$option["template_body"]	                = $objeto->sys_module . "html/report_body";
		
		$data										=$objeto->devices($option);
		$objeto->words["module_body"]				=$data["html"];	
    }
    else
    {
        $module_left                                ="";
    	$objeto->words["module_body"]               =$objeto->__VIEW_SHOW($objeto->sys_module . "html/show");	
    	$objeto->sys_fields["name"]["showTitle"]    ="no";
    	$objeto->words                              =$objeto->__INPUT($objeto->words,$objeto->sys_fields);    
    }

	
	#/*
	$objeto->words["module_title"]              ="Dispositivos $module_title";
	$objeto->words["module_left"]               =$objeto->__BUTTON($module_left);
	$objeto->words["module_center"]             =$module_center;
	$objeto->words["module_right"]              =$objeto->__BUTTON($module_right);;
	
	#if()
	
	#$objeto->__PRINT_R($_SESSION["user"]);
	$objeto->words["html_head_title"]           ="SOLES GPS :: {$_SESSION["company"]["razonSocial"]} :: {$objeto->words["module_title"]}";
	#*/
	
	$objeto->words["html_head_description"]	=	"EN LA EMPRESA SOLESGPS, CONTAMOS CON UN MODULO PARA ADMINISTRAR EL REGISTRO DE ARCHIVOS.";
	$objeto->words["html_head_keywords"] 	=	"GPS, RASTREO, MANZANILLO, SATELITAL, CELULAR, VEHICULAR, VEHICULO, TRACTO, LOCALIZACION, COLIMA, SOLES, SATELITE, GEOCERCAS, STREET VIEW, MAPA";
		
    $objeto->html                               =$objeto->__VIEW_TEMPLATE("system", $objeto->words);
    $objeto->__VIEW($objeto->html);
?>
