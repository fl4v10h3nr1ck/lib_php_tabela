

function seleciona(id_tab, id_objeto){
	
	$("."+id_tab).css("background", "#FFF");
	
	$("."+id_tab).removeClass("selecionado");

	$("#"+id_tab+"_"+id_objeto).addClass("selecionado");
	
	$("#"+id_tab+"_"+id_objeto).css("background", "#32baff");
}




	
function pesquisar(id_tab){
	
	
	$("#area_conteudo_tab_"+id_tab).html(id_tab+"<img src='"+$("#tab__path_"+id_tab).val()+"imgs/load.gif' style='margin-top:50px'>");
	
	$.post(	
	$("#tab__path_"+id_tab).val()+'acao.php',
		{
		nome_da_funcao:"pesquisar", 
		id_tab:id_tab,
		termos:$("#tab_termos_"+id_tab).val()
		},
		function(retorno){ 
	
		$("#area_conteudo_tab_"+id_tab).html(retorno);

		}
	);
}
	
	
	
	

function getIdSelecionado(id_tab){

var id =0;
	$("."+id_tab).each(function(){
	
		if($(this).hasClass("selecionado")){
			
		var aux  =	$(this).attr('id').split("_");
		
		if(aux.length>1)
		id =   aux[1]; 
		
		return;
		}
	});
	
return id;
}





