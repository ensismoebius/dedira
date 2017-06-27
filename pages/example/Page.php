<?php
namespace example;
require_once __DIR__ . '/class/Conf.php';
require_once __DIR__ . '/../../class/page/APage.php';

class Page extends \APage{
	public function __construct() {
		
		echo __("My example page!!!!!!!!!!<br>");
		
		$user = $_SESSION['authData'] ['autenticatedEntity'];
		
		echo __("Hello ") . $user->getLogin();
	}
	
	public static function isRestricted(): bool {
		return true;
	}
}
?>
