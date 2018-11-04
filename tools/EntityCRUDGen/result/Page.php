<?php

namespace CostsCenterEditor;

require_once __DIR__ . '/class/Conf.php';
require_once __DIR__ . '/../../class/page/APage.php';
require_once __DIR__ . '/../../class/template/TemplateLoader.php';
require_once __DIR__ . '/../../class/database/POPOs/user/CostsCenter.php';
require_once __DIR__ . '/../../class/protocols/http/HttpRequest.php';
require_once __DIR__ . '/../../class/internationalization/i18n.php';
/**
 * Register the CostsCenter on system
 * 
 * @author André Furlan
 */
class Page extends \APage {
	
	/**
	 * Manages the templates
	 * 
	 * @var \TemplateLoader
	 */
	protected $xTemplate;
	
	/**
	 * Handles the requests
	 * 
	 * @var \HttpRequest
	 */
	protected $httpRequest;
	public function __construct() {
		\I18n::init ( \Configuration::$defaultLanguage, __DIR__ . "/" . Conf::$localeDirName );
		$this->xTemplate = new \TemplateLoader ( Conf::getTemplate () );
		$this->reflector = new \ReflectionClass ( "CostsCenter" );
		$this->httpRequest = new \HttpRequest ();
		$this->handleRequest ();
	}
	
	/**
	 * Handles request
	 * 
	 * @return void | boolean
	 */
	public function handleRequest() {
		
		// get the next page
		$httpRequest = new \HttpRequest ();
		$gotVars = $httpRequest->getGetRequest ();
		$nextPage = isset ( $gotVars ["page"] ) ? $gotVars ["page"] : \Configuration::$mainPageName;
		
		if (! $this->checkMandatoryFields ()) {
			$this->showGui ( $nextPage );
			return;
		}
		
		// Gets the obj id
		$id = isset ( $gotVars ["id"] ) ? $gotVars ["id"] : null;
		
		// If it does not exists create a new one
		if (is_null ( $id )) {
			if ($this->save ()) {
				$this->xTemplate->assign ( "message", gettext( "Saved!" ) );
			} else {
				$this->xTemplate->assign ( "message", gettext( "Fail to save!" ) );
			}
		} else {
			// Otherwise just updates
			if ($this->update ( $id )) {
				$this->xTemplate->assign ( "message", gettext( "Saved!" ) );
			} else {
				$this->xTemplate->assign ( "message", gettext( "Fail on update!" ) );
			}
		}
		
		$this->showGui ( $nextPage );
	}
	
	/**
	 * Updates CostsCenter
	 *
	 * @param \CostsCenter $user
	 * @return bool
	 */
	private function update($int): bool {
		$obj = $this->createEntityObject ( $int );
		
		// Updating object
		$c = new \DatabaseConditions ();
		$c->addCondition ( \DatabaseConditions::AND, "id", $obj->get_id () );
		
		$query = new \DatabaseQuery ();
		$query->setConditions ( $c );
		$query->setObject ( $obj );
		$query->setOperationType ( \DatabaseQuery::OPERATION_UPDATE );
		
		return \Database::execute ( $query );
	}
	
	/**
	 * Create a new CostsCenter
	 * 
	 * @return bool
	 */
	private function save(): bool {
		$obj = $this->createEntityObject ();
		
		// Inserting object
		$query = new \DatabaseQuery ();
		$query->setObject ( $obj );
		$query->setOperationType ( \DatabaseQuery::OPERATION_PUT );
		
		return \Database::execute ( $query );
	}
	
	/**
	 * Creates a CostsCenter object using previous data or not
	 * 
	 * @param
	 *        	$id
	 * @return \CostsCenter
	 */
	private function createEntityObject($id = null): \CostsCenter {
		$arrMethods = $this->reflector->getMethods ( \ReflectionMethod::IS_PUBLIC );
		$postedVars = $this->httpRequest->getPostRequest ();
		
		// Creates a new object
		$obj = new \CostsCenter ();
		if (! is_null ( $id )) {
			$obj->set_id ( $id );
		}
		
		foreach ( $arrMethods as $method ) {
			if ($method->getNumberOfParameters () == 1) {
				
				if (substr ( $method->getName (), 0, 3 ) == "set") {
					$method->invoke ( $obj, $postedVars [$method->getParameters () [0]] );
				}
			}
		}
		return obj;
	}
	private function checkMandatoryFields(): bool {
		$postedVars = $this->httpRequest->getPostRequest ();
		
		// Check mandatory fields
		foreach ( $postedVars as $var ) {
			if (trim ( $var ) == "") {
				return false;
			}
		}
		
		return true;
	}
	private function showGui(string $nextPage) {
		$postedVars = $this->httpRequest->getPostRequest ();
		
		foreach ( $postedVars as $name => $value ) {
			$this->xTemplate->assign ( $name, $value );
		}
		
		$this->xTemplate->assign ( "nextPage", $nextPage );
		$this->xTemplate->parse ( "main" );
		$this->xTemplate->out ( "main" );
	}
	public static function isRestricted(): bool {
		return false;
	}
	protected function generateOutput($object): string {
	}
	protected function setup(): bool {
		return true;
	}
}
?>
