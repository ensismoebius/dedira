<?php
require_once 'Class/Core/BaseDeDados/CouchDb/CCouchDB.php';
require_once 'Class/Core/Configuracao/CConfiguracao.php';

/**
 * Responsável pelo gerenciamento do armazenamento dos dados
 * @author andre
 *
 */
class CBaseDeDados extends CCouchDB{

	private $baseSelecionada;

	private $resposta;

	public function __construct(){
		parent::__construct();
	}

	/**
	 * Criar um nova base de dados
	 * @param string $nomeDaBase
	 * @return boolean
	 */
	public function criarBaseDeDados($nomeDaBase){

		$nomeDaBase = substr($nomeDaBase,-1,1) == "/" ? $nomeDaBase : "/" . $nomeDaBase;

		$this->enviar(self::CONST_OPERACAO_PUT, $nomeDaBase);

		$this->resposta = $this->getResultadoDaConsulta();

		if(!isset($this->resposta->ok) && !$this->resposta->ok) return false;

		$this->selecionarBaseDeDados($nomeDaBase);

		return true;
	}

	/**
	 * Apaga uma base de dados
	 * @param string $nomeDaBase
	 * @return boolean
	 */
	public function apagaBaseDeDados($nomeDaBase){

		$nomeDaBase = substr($nomeDaBase,-1,1) == "/" ? $nomeDaBase : "/" . $nomeDaBase;

		$this->enviar(self::CONST_OPERACAO_DEL, $nomeDaBase);

		$this->resposta = $this->getResultadoDaConsulta();

		if(!isset($this->resposta->ok) && !$this->resposta->ok) return false;

		$this->selecionarBaseDeDados("");

		return true;
	}

	/**
	 * Seleciona a base dados que sofrerá as leituras e escritas
	 * @param string $nomeDaBase
	 */
	public function selecionarBaseDeDados($nomeDaBase){
		$nomeDaBase = substr($nomeDaBase,-1,1) == "/" ? $nomeDaBase : "/" . $nomeDaBase;
		
		$this->baseSelecionada = $nomeDaBase;
	}

	/**
	 * Insere uma nova informação na base de dados
	 * @param mixed $informacao
	 * @return boolean
	 */
	public function inserirInformacao($idDoDocumento, $informacao){

		if($idDoDocumento == ""){
			$this->enviar(self::CONST_OPERACAO_POST, $this->baseSelecionada, $idDoDocumento, $informacao);
		}else{
			$this->enviar(self::CONST_OPERACAO_PUT, $this->baseSelecionada, $idDoDocumento, $informacao);
		}

		$this->resposta = $this->getResultadoDaConsulta();

		if(!isset($this->resposta->ok) && !$this->resposta->ok){
			return false;
		}

		return true;
	}

	public function apagarInformacao($idDoDocumento, $revisao){

		$this->enviar(self::CONST_OPERACAO_DEL, $this->baseSelecionada, $idDoDocumento, null, $revisao);

		$this->resposta = $this->getResultadoDaConsulta();

		if(!isset($this->resposta->ok) && !$this->resposta->ok){
			return false;
		}

		return true;
	}

	public function atualizarInformacao($idDoDocumento, $revisao, $informacao){

		$this->enviar(self::CONST_OPERACAO_PUT, $this->baseSelecionada, $idDoDocumento, $informacao, $revisao);

		$this->resposta = $this->getResultadoDaConsulta();

		if(!isset($this->resposta->ok) && !$this->resposta->ok){
			return false;
		}

		return true;
	}

	public function carregarInformacao($idDoDocumento){

		$this->enviar(self::CONST_OPERACAO_GET, $this->baseSelecionada, $idDoDocumento);

		$this->resposta = $this->getResultadoDaConsulta();

		if(isset($this->resposta->error)){
			return false;
		}

		return true;
	}

	public function getResposta(){
		return $this->resposta;
	}
}
?>