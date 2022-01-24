<?php

if(!isset($_SESSION))
session_start();
 
 
chdir(dirname(__FILE__)); 

include_once getcwd().'/define.php';

include_once TAB_LOCAL_ABS_ANOTACOES.'annotations.php';

include_once TAB_BASE_PACK_ABS.'AnotColuna.class.php';

include_once TAB_BASE_PACK_ABS.'AnotQuery.class.php';

include_once TAB_LOCAL_ABS_BD.'bd_util.class.php';




	function ordenador($a, $b){
		return ($a['posicao']==$b['posicao']?0:($a['posicao']<$b['posicao']?-1:1));
	}
	

	

final class Tabela{


private $bd;





	function __construct($id_tab=null) {

	if($id_tab==null)
	$_SESSION["tab_objeto"] =null;
	
	$this->bd = new bd_util();	
	}


	

	
	
	
	public function dependencias(){
	
	echo "<script src='".TAB_BASE_PACK_SPS."tabela.js' type='text/javascript'></script>";	
	
	echo "<link rel='stylesheet' href='".TAB_BASE_PACK_SPS."tabela.css' type='text/css' media='all'>";
	}

	
	
	
	
	
	public function getTabela($objeto, $path_abs_objeto){
	
	if(!is_object($objeto))
	return;
	
	$metaDados = $this->getMetaDadosDeCabecalho($objeto);
	
	if($metaDados==null)
	return;

	$id_tab=rand(0, 999999); 
	
	if(!array_key_exists("tab_objeto", $_SESSION) || $_SESSION["tab_objeto"]==null)
	$_SESSION["tab_objeto"] = array();
	
	$_SESSION["tab_objeto"][]=array($id_tab=>serialize($objeto), 
										"path"=>$path_abs_objeto, 
											"nome_classe"=>get_class($objeto));

	
	$form = 
	"<div class='tab_local' align ='center'>
	 <input type='hidden' id='tab__path_".$id_tab."' value='".TAB_BASE_PACK_SPS."'>
		<div class='tab_area_pesquisa'>
			<div class='area_termo_pesquisa' align='left'>
			Pesquisar:<br>
			<input type='text' id='tab_termos_".$id_tab."' class='tab_termos' onKeyPress=\"if ((window.event ? event.keyCode : event.which) == 13) { pesquisar(".$id_tab."); }\">
			</div>
			<div class='area_bt_pesquisar' align='left'>
			<button id='tab_bt_pesquisar_".$id_tab."' onclick='javascript:pesquisar(".$id_tab.")' style='margin-top:15px'><img src='".TAB_BASE_PACK_SPS."imgs/pesquisar.png' style='width:25px'></button>	
			</div>
			<div style='clear:both'></div>
		</div>
		<table border= '1'  class = 'tab_cabecalho'  align ='center' cellspacing ='1'>
			<tr>";
		
	foreach($metaDados as $valor)	
	$form .= "<th width='".$valor['comprimento']."%' align='center'>".$valor['rotulo']."</th>";
	
	$form .= "
			</tr>
		</table>";
	
	
	$form .= "
		<div id='area_conteudo_tab_".$id_tab."' align='center' class='area_conteudo_tab'>
		".$this->getValores($id_tab).
		"</div>
	</div>";
	
	return $form;
	}
	
	
	
	
	
	
		
	
	
	private function getValores($id_tab){

	$objeto = null;
	
		if(count($_SESSION["tab_objeto"])>0){
			
			foreach($_SESSION["tab_objeto"] as $item){
			
				if(count($item)>0 && array_key_exists($id_tab, $item)){

				include_once $item['path'].$item['nome_classe'].".class.php";
				
				$objeto = unserialize($item[$id_tab]);
				break;
				}	
			}
		}
		else
		return $this->erro("COD: 001");

	
	if(!is_object($objeto))
	return $this->erro("COD: 002");
	
	$metadados = $this->getMetaDadosDePesquisa($objeto);
	
	if($metadados==null)
	return $this->erro();


	$query_where = $metadados['where'].
			(strlen($metadados['where'])>0?" AND ": " ").
			$this->getQueryTermos($objeto, $metadados);



	$reg = $this->bd->getPorQuery($objeto, null, $query_where, $metadados['orderby']);	
		
	$resultado = "<table border= '1'  class = 'tab_conteudo'  align ='center' cellspacing ='1'>";
	
		if(count($reg)>0){
			
			foreach($reg as $bean){
			
			$id = $this->bd->getValorDeCampoId($bean);
			
			
			$resultado .="<tr class='".$id_tab."' id='".$id_tab."_".$id."' onclick='javascript:seleciona(".$id_tab.", ".$id.")'>";
			
				foreach($metadados['props'] as $meta){
				
			
				$resultado .="<td width='".$meta['comprimento']."%' align='".$meta['alinhamento']."'>".(strlen($meta['func_composicao'])>0?$bean->$meta['func_composicao']():$bean->$meta['nome_prop'])."</td>";
				
				
				}
				
			$resultado .="</tr>";
			}	
		}
		else{
			
			
		}
	
	$resultado .= "</table>";
	
	return $resultado;
	}
	
	
	
	
	
	


	
	public function getQueryTermos($objeto, $metadados){
	
	$termos = array_key_exists("termos", $_POST)?$_POST["termos"]:"";
	
	if( strlen( $termos ) == 0) 
	return " 1 ";
	
	$locais = array();
	
	
		foreach($metadados['props'] as $meta){
			
			if($meta['nao_pesquisar'])
			continue;
		
		$locais[]=$this->bd->getCampoDeProp($objeto, $meta['nome_prop']);	
		}
	

	//print_r ($locais);
	
	$tokens_do_termo = explode(' ', $termos);
	
	$query_termo = " (";
		
		foreach($locais as $i=>$local){
		
		$query_termo .= "(";
		
			foreach($tokens_do_termo as $j => $value){
			
			$query_termo .= $local. " like '%".$value."%'";
			
			if( $j < count($tokens_do_termo) -1)
			$query_termo .= " AND ";
			}
		
		$query_termo .= ")";
		
		
		if( $i < count($locais) -1)
		$query_termo .= " OR ";		
		}
		
	return $query_termo.")";	
	}
	
	
	
	
	
	

	
	public function pesquisar($id_tab){
	
	echo $this->getValores($id_tab);
	}
	
	
	

	
	
	
	
	private function getMetaDadosDeCabecalho($objeto){
	
	$reflexao_classe = new ReflectionClass($objeto);
	
	$props = $reflexao_classe->getProperties(ReflectionProperty::IS_PUBLIC | 
												ReflectionProperty::IS_PROTECTED);
	
	if(count($props)==0)
	return null;			
	
	
	$metaDados = array();
	
		foreach($props as $prop){
		
		$reflexao_prop = new ReflectionAnnotatedProperty($objeto, $prop->getName());
	
			if($reflexao_prop!=null){
			
			
			$metaDados[] = array(
								 "rotulo"=>$reflexao_prop->getAnnotation('AnotColuna')->rotulo, 
								 "posicao"=>$reflexao_prop->getAnnotation('AnotColuna')->posicao, 
								 "comprimento"=>$reflexao_prop->getAnnotation('AnotColuna')->comprimento);
			}
		}
	
		if($metaDados!=null && count($metaDados)>0){
		
		usort($metaDados, "ordenador"); 

		return $metaDados;
		}
		
	return null;
	}
	
	
	
	
	
	
	
	private function getMetaDadosDePesquisa($objeto){
	
	$anot_classe = new ReflectionAnnotatedClass($objeto);
	
	$metadados = array();

		if($anot_classe->hasAnnotation('AnotQuery')){
			
		$metadados['where']  = $anot_classe->getAnnotation('AnotQuery')->where;
		$metadados['orderby']  = $anot_classe->getAnnotation('AnotQuery')->orderby;
		}

	
	$reflexao_classe = new ReflectionClass($objeto);
	
	$props = $reflexao_classe->getProperties(ReflectionProperty::IS_PUBLIC | 
												ReflectionProperty::IS_PROTECTED);
	
	if(count($props)==0)
	return null;			
	
	
	$metadados['props'] = array();
	
		foreach($props as $prop){
		
		$reflexao_prop = new ReflectionAnnotatedProperty($objeto, $prop->getName());
	
			if($reflexao_prop!=null){
			
			
			$metadados['props'][] = array(
								 "posicao"=>$reflexao_prop->getAnnotation('AnotColuna')->posicao, 
								 "comprimento"=>$reflexao_prop->getAnnotation('AnotColuna')->comprimento, 
								 "nao_pesquisar"=>$reflexao_prop->getAnnotation('AnotColuna')->nao_pesquisar, 
								 "alinhamento"=>($reflexao_prop->getAnnotation('AnotColuna')->alinhamento!=null?$reflexao_prop->getAnnotation('AnotColuna')->alinhamento:"center"),
								 "func_composicao"=>($reflexao_prop->getAnnotation('AnotColuna')->func_composicao!=null?$reflexao_prop->getAnnotation('AnotColuna')->func_composicao:null),
								 "nome_prop"=>$prop->getName());
			}
		}
	
	
		if($metadados!=null && count($metadados)>0 && count($metadados['props'])>0){
		
		usort($metadados['props'], "ordenador"); 

		return $metadados;
		}
		
	return null;
	}
	
	
	
	
	
	
	
	
	private function erro($cod=""){
		
	return "erro (".$cod.")";	
	}
	
	
	


}

?>