<?php
require_once __DIR__ . '/../database/StorableObject.php';

class Person extends StorableObject{

	/**
	 * Nome
	 * @var string
	 */
	protected $name;

	/**
	 * Sobrenome
	 * @var string
	 */
	protected $secondName;

	/**
	 * Data de nascimento
	 * @var Datetime
	 */
	protected $birthDate;

	/**
	 * Sexo do usuário
	 * @var char
	 */
	protected $sex;

	/**
	 * Emails do usuário
	 * @var array : string
	 */
	protected $arrEmail;

	/**
	 * Telefones do usuário
	 * @var array : string
	 */
	protected $arrTelefone;

	public function getName(){
		return $this->name;
	}

	public function setName($name){
		$this->name = $name;
	}

	public function getSecondName(){
		return $this->secondName;
	}

	public function setSecondName($secondName){
		$this->secondName = $secondName;
	}

	public function getBirthDate(){
		return $this->birthDate;
	}

	public function setBirthDate($birthDate){
		$this->birthDate = $birthDate;
	}

	public function getSex(){
		return $this->sex;
	}

	public function setSex($sex){
		$this->sex = $sex;
	}

	public function getArrEmail(){
		return $this->arrEmail;
	}

	public function setArrEmail($arrEmail){
		$this->arrEmail = $arrEmail;
	}

	public function getArrTelefone(){
		return $this->arrTelefone;
	}

	public function setArrTelefone($arrTelefone){
		$this->arrTelefone = $arrTelefone;
	}
}
?>