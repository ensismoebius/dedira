<?php
namespace userAuthenticaticator;

require_once __DIR__ . '/../../../class/configuration/Configuration.php';

final class UserAuthenticaticatorConf extends \Configuration {
	public static function getAutenticationRequestTemplate() {
		return __DIR__ . "/../template/UserAuthenticaticator.html";
	}
}
?>