<?php
require_once __DIR__ . '/../log/Log.php';
require_once __DIR__ . '/../protocols/http/HttpRequest.php';
require_once __DIR__ . '/../configuration/Configuration.php';
require_once __DIR__ . '/../security/authentication/Authenticator.php';
/**
 * Handles the pages
 *
 * @author ensismoebius
 *        
 */
class Page {
	/**
	 * Executes the specified page
	 *
	 * @param string $pageId        	
	 * @return boolean
	 */
	public static function loadPage($pageId = null): bool {
		try {
			$pageId = self::loadPageAndValidatePageId ( $pageId );
		} catch ( Exception $error ) {
			Log::recordEntry ( "There is not such page" );
			return false;
		}
		// Even the page has the "isRestricted()" method
		// it MUST implement the IPage interface!
		if (! in_array ( "IPage", class_implements ( "$pageId\\Page" ) )) {
			Log::recordEntry ( "The page MUST implement the IPage interface!" );
			return false;
		}
		
		// Executes the page!!!!
		$class = new ReflectionClass ( "$pageId\\Page" );
		$class->newInstance ( null );
		return true;
	}
	
	/**
	 * Return the page id, if no page is
	 * specified than return the main page
	 *
	 * @return string
	 */
	private static function loadPageAndValidatePageId($pageId = null) {
		$auth = new Authenticator ();
		$httpRequest = new HttpRequest ();
		
		// If no page id was informed retrieves one
		if (is_null ( $pageId )) {
			$pageId = $httpRequest->getGetRequest ( Configuration::PAGE_VAR_NAME ) [0];
			$pageId = is_null ( $pageId ) ? Configuration::MAIN_PAGE_NAME : $pageId;
		}
		
		// Checks if the page exists if no returns the authentication page
		if (! file_exists ( Configuration::getPagesDiretory () . DIRECTORY_SEPARATOR . $pageId . DIRECTORY_SEPARATOR . Configuration::getPageFileName () )) {
			throw new Exception ( "There is not such page" );
		}
		
		// Loads the page
		require_once Configuration::getPagesDiretory () . DIRECTORY_SEPARATOR . $pageId . DIRECTORY_SEPARATOR . Configuration::getPageFileName ();
		
		// If page is restricted we have to be authenticated to use it
		if (self::isRestrictedPage ( $pageId )) {
			if ($auth->isAuthenticated ()) return $pageId;
			
			// Otherwise go to authentication page
			$pageId = Configuration::AUTHENTICATION_PAGE_NAME;
			require_once Configuration::getPagesDiretory () . DIRECTORY_SEPARATOR . $pageId . DIRECTORY_SEPARATOR . Configuration::getPageFileName ();
		}
		
		// If is a open page, just open it
		return $pageId;
	}
	
	/**
	 * Is the page restricted?
	 *
	 * @param string $pageId        	
	 * @return bool
	 */
	private static function isRestrictedPage($pageId): bool {
		$restricted = true;
		eval ( "\$restricted =  $pageId\\Page::isRestricted();" );
		return $restricted;
	}
}
?>