<?php
require_once __DIR__ . '/../Configuration.php';

class LocalizationSearcherConf extends Configuration{
	
	const CEP_LIVRE_SERVICE_URL = "http://ceplivre.pc2consultoria.com/index.php";
	const CEP_UNICO_SERVICE_URL = "http://republicavirtual.com.br/web_cep.php";
	
	public static function getCepLivreUrl(){
		return self::CEP_LIVRE_SERVICE_URL . "?module=cep&format=xml&cep=";
	}
	
	public static function getCepUnicoUrl(){
		return self::CEP_UNICO_SERVICE_URL . "?formato=xml&cep=";
	}
}
?>