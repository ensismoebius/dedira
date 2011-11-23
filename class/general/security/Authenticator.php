<?php
require_once __DIR__ . '/../log/Log.php';

class Authenticator{

	/**
	 * Regras de autenticação
	 * @var IAuthenticationRules
	 */
	private $authenticationRules;
	
	/**
	 * Id do usuário na sessão
	 * @var int | string
	 */
	private $authenticationId;

	/**
	 * Incia o autenticado de usuários no sistema
	 * @param IAuthenticationRules $authenticationRules
	 */
	public function __construct(IAuthenticationRules $authenticationRules = null){
		$this->authenticationRules = $authenticationRules;
	}

	/**
	 * Retorna a id do usuário na sessão
	 * @return int | string
	 */
	public function getUserId(){
		if(!isset($_SESSION)) session_start();
		return $_SESSION['userData']['userId'];
	}

	/**
	 * Informa se o usário está autenticado no sistema
	 * @return boolean
	 */
	public function isAuthenticated(){
		if(!isset($_SESSION)) session_start();
		return isset($_SESSION['userData']['userId']);
	}

	/**
	 * Desautentica o usuário do sistema
	 */
	public function unauthenticate(){
		if(!isset($_SESSION)) session_start();
		unset($_SESSION);
		@session_destroy();
	}

	/**
	 * Autentica o usuário no sistema
	 * @return boolean
	 */
	public function authenticate(){

		try{
			//Verificando se a verificação de usuário e senha retorna o tipo de valor esperado (booleano)
			$isValid = $this->authenticationRules->verifyUserAndPassword();
			if(!is_bool($isValid)) throw new SystemException("O procedimento 'verifyUserAndPassword' deve retornar um valor booleano.",__CLASS__ .__LINE__);

			//Caso a verificação esteja ok, verifica se o usuário e senha são válidos
			if($isValid){
				
				//Se o usuário e senha são válidos recupera a id do usuário na sessão
				$authenticationId = $this->authenticationRules->getAutenticationId();
				if(!(is_numeric($authenticationId) || is_string($authenticationId))) throw new SystemException("O procedimento 'getAutenticationId' deve retornar uma string ou um número.",__CLASS__ .__LINE__);
				
				@session_destroy();
				//Se tudo deu certo incia a sessão e atribui o id do usuário
				session_start();
				session_regenerate_id();
				$_SESSION['userData']['userId'] = $authenticationId;
				
				$this->authenticationId = $authenticationId;
				
				return true;
			}

		}catch(Exception $e){
			new Log($e->getMessage());
			throw new SystemException($e->getMessage(),__CLASS__ .__LINE__);
		}
		//login ou senha inválidos
		return false;
	}

	public function setAuthenticationRules(IAuthenticationRules $authenticationRules){
		$this->authenticationRules = $authenticationRules;
	}

	/**
	 * Verifica se houve uma solicitação de autenticação na página
	 */
	public function isAuthenticationRequested(){
		return isset($_POST["login"]) && isset($_POST["password"]);
	}

	public function getAuthenticationId(){
		return $this->authenticationId;
	}
}
?>