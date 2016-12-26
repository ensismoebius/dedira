<?php
namespace rulesEditor;
require_once __DIR__ . '/Conf.php';
final class Lang_Configuration {
	public static function getDescriptions( $descriptionId ){
		$languages = array ();
		
		// Português Brasil
		$languages ["pt_BR"] = array (
				0 => "Salvo com sucesso", 
				1 => "Falha ao salvar",
		);
		
		// English United States
		$languages ["en_US"] = array (
				0 => "Saved!",
				1 => "Fail to save!"
		);
		
		return $languages [Conf::getSelectedLanguage ()] [$descriptionId];
	}
}
?>