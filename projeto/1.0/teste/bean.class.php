<?php



/** @Anot_tabela(nome="usuarios_sistema", prefixo="uss")
	@AnotQuery(where="", orderby="###.id_usuario ASC") */
final class bean{


/** @AnotColuna(rotulo="COD", posicao=1, comprimento=10)
	@AnotCampo(nome="id_usuario", tipo="int", ehId=true) */
public $id;

/** @AnotColuna(rotulo="Nome", posicao=2, comprimento=40, alinhamento="left") 
	@AnotCampo(nome="usuario") */
public $nome;

/** @AnotColuna(rotulo="COD", posicao=3, comprimento=30, alinhamento="left") 
	@AnotCampo(nome="email") */
public $email;

/** @AnotColuna(rotulo="Status", posicao=5, comprimento=10, func_composicao="getStatus") 
	@AnotCampo(nome="status", tipo="int") */
public $stts;

/** @AnotColuna(rotulo="Cadastro", posicao=4, comprimento=10) 
	@AnotCampo(nome="data_cadastro", tipo="data") */
public $data_cad;





	public function getStatus(){
		
		if($this->stts<=0)
			return "<font color='red'>INATIVO</font>";
		else
			return "<font color='green'>ATIVO</font>";
	}

}
?>