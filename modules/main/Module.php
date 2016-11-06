<?php

namespace main;

require_once __DIR__ . '/class/MainConf.php';
require_once __DIR__ . '/class/Lang_Configuration.php';
require_once __DIR__ . '/../../class/database/POPOs/user/User.php';
require_once __DIR__ . '/../../class/template/TemplateLoader.php';
require_once __DIR__ . '/../../class/security/authentication/Authenticator.php';
class Module {
	
	/**
	 * Gerencia os templates
	 *
	 * @var XTemplate
	 */
	protected $xTemplate;
	public function __construct() {
		$auth = new \Authenticator ();
		$user = $auth->getAutenticatedEntity ();
		
		$this->xTemplate = new \TemplateLoader ( MainConf::getMainTemplate () );
		$this->xTemplate->assign ( "wellcomeMessage", Lang_Configuration::getDescriptions ( 0 ) );
		$this->xTemplate->assign ( "userName", $user->getName () );
		
		$this->xTemplate->parse ( "main" );
		$this->xTemplate->out ( "main" );
	}
}
new Module ();
?>
