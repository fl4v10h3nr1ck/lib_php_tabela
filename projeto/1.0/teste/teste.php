<?php
header('Content-type: text/html; charset=UTF-8');

include_once 'bean.class.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/tabela/projeto/1.0/tabela.class.php';

chdir(dirname(__FILE__)); 

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt-br" lang="pt-br">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />


<?php

$tab = new tabela();


echo "<script src='/jquery-3.1.1.min.js' type='text/javascript'></script>";	
	
echo $tab->dependencias();

echo "</head>";

echo "<body>";


echo $tab->getTabela(new bean(), getcwd()."/");


echo "</body>";

echo "</html>";
?>