<?php
require_once __DIR__ . '/IOrganizacao.php';
require_once __DIR__ . '/../general/database/StorableObject.php';
class Organizacao extends AStorableObject implements IOrganizacao {
	
	/**
	 * Tipo da organização social
	 * 
	 * @example Camponesa, Sindical, Estudantil, etc
	 * @var TipoOrganizacao
	 */
	protected $tipo;
	protected $nome;
	protected $descricao;
	protected $arrContatos;
	protected $arrFocosDeAtuacao;
	
	/**
	 * SubOrganizações podem ser interpretadas como as frentes
	 * 
	 * @var Array : IOrganizacao
	 */
	protected $arrSubOrganizacoes;
	
	/**
	 * Informa a organização pai da frente se for "null" é uma organização raíz
	 * 
	 * @var OrganizacaoPolitica
	 */
	protected $organizacaoPai;
	public function __construct() {
		parent::__construct ();
		$this->organizacaoPai = null;
	}
	public function getTipo() {
		return $this->tipo;
	}
	public function setTipo(TipoOrganizacao $tipo) {
		$this->tipo = $tipo;
	}
	public function getNome() {
		return $this->nome;
	}
	public function setNome($nome) {
		$this->nome = $nome;
	}
	public function getDescricao() {
		return $this->descricao;
	}
	public function setDescricao($descricao) {
		$this->descricao = $descricao;
	}
	public function getArrContatos() {
		return $this->arrContatos;
	}
	public function setArrContatos($arrContatos) {
		$this->arrContatos = $arrContatos;
	}
	public function getArrFocosDeAtuacao() {
		return $this->arrFocosDeAtuacao;
	}
	public function setArrFocosDeAtuacao($arrFocosDeAtuacao) {
		$this->arrFocosDeAtuacao = $arrFocosDeAtuacao;
	}
	public function getArrSubOrganizacoes() {
		return $this->arrSubOrganizacoes;
	}
	public function setArrSubOrganizacoes($arrSubOrganizacoes) {
		$this->arrSubOrganizacoes = $arrSubOrganizacoes;
	}
	public function getOrganizacaoPai() {
		return $this->organizacaoPai;
	}
	public function setOrganizacaoPai(OrganizacaoPolitica $organizacaoPai) {
		$this->organizacaoPai = $organizacaoPai;
	}
}