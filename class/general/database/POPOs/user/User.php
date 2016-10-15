<?php
require_once __DIR__ . '/../person/Person.php';
/**
 * Representa um usuário no sistema
 *
 * @author André Furlan
 *         @Entity
 */
class User extends Person {
	
	/**
	 * Login
	 *
	 * @var string @Column(nullable = false)
	 */
	protected $login;
	
	/**
	 * Senha
	 *
	 * @var string @Column(nullable = false)
	 */
	protected $password;
	
	/**
	 * Identificação do usuário
	 *
	 * @var int @Id
	 *      @GeneratedValue
	 */
	protected $id;
	
	/**
	 * Indica se o usuário está ativo ou não
	 *
	 * @var boolean @Column(nullable = false)
	 */
	protected $active;
	
	/**
	 * Indica qual o grupo de acesso o usuário pertence
	 *
	 * @var Group @Column(nullable = false)
	 */
	protected $accessGroup;
	
	/**
	 * User constructor
	 */
	public function __construct() {
		$this->active = true;
	}
	public function getId() {
		return $this->id;
	}
	public function setId($id) {
		$this->id = $id;
		$this->AddChange ( "id", $id );
	}
	public function getLogin() {
		return $this->login;
	}
	public function setLogin($login) {
		$this->login = $login;
		$this->AddChange ( "login", $login );
	}
	public function getPassword() {
		return $this->password;
	}
	public function setPassword($password) {
		$this->password = $password;
		$this->AddChange ( "password", $password );
	}
	public function getActive(): bool {
		return $this->active;
	}
	public function setActive(bool $active) {
		if (! is_bool ( $active ))
			throw new SystemException ( "A boolean must be informed.", __CLASS__ . __LINE__ );
		$this->active = $active;
		$this->AddChange ( "active", $active );
	}
	public function getAccessGroup() {
		return $this->accessGroup;
	}
	public function setAccessGroup(Group $accessGroup) {
		$this->accessGroup = $accessGroup;
		$this->AddChange ( "accessGroup", $accessGroup );
	}
}
?>