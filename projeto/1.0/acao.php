<?php

header('Content-type: text/html; charset=UTF-8');

chdir(dirname(__FILE__)); 

include_once getcwd().'/Tabela.class.php';


$tab = new Tabela($_POST['id_tab']);


	if( array_key_exists("nome_da_funcao", $_POST) ){

	if (method_exists($tab, $_POST["nome_da_funcao"])) 
	call_user_func(array($tab, $_POST["nome_da_funcao"]), $_POST['id_tab']);
	}
	
?>