<?php
require_once __DIR__ . '/CouchDb/CCouchDB.php';
require_once __DIR__ . '/../../Core/Configuration/Configuration.php';
/**
 * Responsável pelo gerenciamento do armazenamento dos dados
 * @author andre
 *
 */
class CDatabase extends CCouchDB{

	/**
	 * Indica qual base de dados está selecionada 
	 * @var string
	 */
	private $selectedBaseName;

	private $response;

	public function __construct(){
		parent::__construct();
	}

	/**
	 * Criar um nova base de dados
	 * @param string $dataBaseName
	 * @return boolean
	 */
	public function createDatabase($dataBaseName){

		$dataBaseName = substr($dataBaseName,-1,1) == "/" ? $dataBaseName : "/" . $dataBaseName;

		$this->send(self::CONST_PUT_OPERATION, $dataBaseName);

		$this->response = $this->getRequestResponse();

		if(!isset($this->response->ok) && !$this->response->ok) return false;

		$this->databaseSelect($dataBaseName);

		return true;
	}

	/**
	 * Apaga uma base de dados
	 * @param string $dataBaseName
	 * @return boolean
	 */
	public function apagaBaseDeDados($dataBaseName){

		$dataBaseName = substr($dataBaseName,-1,1) == "/" ? $dataBaseName : "/" . $dataBaseName;

		$this->send(self::CONST_DEL_OPERATION, $dataBaseName);

		$this->response = $this->getRequestResponse();

		if(!isset($this->response->ok) && !$this->response->ok) return false;

		$this->databaseSelect("");

		return true;
	}

	/**
	 * Seleciona a base dados que sofrerá as leituras e escritas
	 * @param string $dataBaseName
	 */
	public function databaseSelect($dataBaseName){
		$dataBaseName = substr($dataBaseName,-1,1) == "/" ? $dataBaseName : "/" . $dataBaseName;

		$this->selectedBaseName = $dataBaseName;
	}

	/**
	 * Insere uma nova informação na base de dados
	 * @param mixed $arrInformation
	 * @return boolean
	 */
	public function saveDocument($documentId, $arrInformation){

		if(!is_array($arrInformation)) throw new Exception("text - A informação fornecida deve ser um arranjo");

		if($documentId == ""){
			$this->send(self::CONST_POST_OPERATION, $this->selectedBaseName, $documentId, $arrInformation);
		}else{
			$this->send(self::CONST_PUT_OPERATION, $this->selectedBaseName, $documentId, $arrInformation);
		}

		$this->response = $this->getRequestResponse();

		if(!isset($this->response->ok) && !$this->response->ok){
			return false;
		}

		return true;
	}

	public function eraseDocument($documentId, $revisionId){

		$this->send(self::CONST_DEL_OPERATION, $this->selectedBaseName, $documentId, null, $revisionId);

		$this->response = $this->getRequestResponse();

		if(!isset($this->response->ok) && !$this->response->ok){
			return false;
		}

		return true;
	}

	public function updateDocumentInformation($documentId, $revisionId, $arrInformation){

		$this->send(self::CONST_PUT_OPERATION, $this->selectedBaseName, $documentId, $arrInformation, $revisionId);

		$this->response = $this->getRequestResponse();

		if(!isset($this->response->ok) && !$this->response->ok){
			return false;
		}
		return true;
	}

	public function loadDocument($documentId){

		$this->send(self::CONST_GET_OPERATION, $this->selectedBaseName, $documentId);

		$this->response = $this->getRequestResponse();

		if(isset($this->response->error)){
			return false;
		}

		return true;
	}

	/**
	 * Executa a view
	 * @param string $viewAddress
	 */
	public function executeView($viewAddress){
		$this->send(self::CONST_GET_OPERATION,$viewAddress);
		$this->response = $this->getRequestResponse();
	}

	public function loadAllViews(){

		$arrFinalResponse = array();

		$this->send(self::CONST_GET_OPERATION, $this->selectedBaseName . "/_all_docs?descending=true&startkey=\"_design0\"&endkey=\"_design\"");

		$this->response = $this->getRequestResponse();

		if(isset($this->response->error)){
			return false;
		}

		$arrViews = $this->response->rows;

		foreach ($arrViews as $view) {
			$this->send(self::CONST_GET_OPERATION, $this->selectedBaseName . "/" . $view->id);
			$this->response = $this->getRequestResponse();
			if(isset($this->response->error)) return false;
			$arrFinalResponse[] = $this->getRequestResponse();
		}

		$this->response = $arrFinalResponse;
		return true;
	}

	public function getResponse(){
		return $this->response;
	}
}
?>