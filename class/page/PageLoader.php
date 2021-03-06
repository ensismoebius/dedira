<?php
require_once __DIR__ . '/../log/Log.php';
require_once __DIR__ . '/../protocols/http/HttpRequest.php';
require_once __DIR__ . '/../configuration/Configuration.php';
require_once __DIR__ . '/../security/authentication/Authenticator.php';

/**
 * Handles the pages
 *
 * @author ensismoebius
 */
class PageLoader {

	private static $nextSeed;

	/**
	 * Executes the specified page
	 *
	 * @param string $pageId
	 * @return boolean
	 */
	public static function loadPage($pageId = null): bool {
		$httpRequest = new HttpRequest ();

		// If no page id was informed retrieves one
		if (is_null ( $pageId )) {
			$pageId = $httpRequest->getGetRequest ( Configuration::$pageParameterName ) [0];
			$pageId = is_null ( $pageId ) ? Configuration::$mainPageName : $pageId;
		}

		try {
			$pageId = self::loadPageAndValidatePageId ( $pageId );
		} catch ( Exception $error ) {
			Log::recordEntry ( "There is not such page: " . $error->getMessage () );
			return false;
		}
		// it MUST implement the APage abstraction!
		if (! is_subclass_of ( "$pageId\\Page", "APage" )) {
			Log::recordEntry ( "The page MUST implement the APage abstraction!" );
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

		// if the page do not exists throws an exception
		if (! file_exists ( Configuration::$pagesDirectory . DIRECTORY_SEPARATOR . $pageId . DIRECTORY_SEPARATOR . Configuration::$defaultPageFileName )) {
			throw new Exception ( "There is not such page" );
		}

		// If page is restricted we have to be authenticated to use it
		require_once Configuration::$pagesDirectory . DIRECTORY_SEPARATOR . $pageId . DIRECTORY_SEPARATOR . Configuration::$defaultPageFileName;
		if (self::isRestrictedPage ( $pageId )) {

			// Return the restricted page id if user are autheticated
			if ($auth->isAuthenticated ()) {
				SessionSeed::genNextSeed();
				return $pageId;
			}

			// Otherwise go to authentication page
			$pageId = Configuration::$authenticationPageName;
			require_once Configuration::$pagesDirectory . DIRECTORY_SEPARATOR . $pageId . DIRECTORY_SEPARATOR . Configuration::$defaultPageFileName;
			return $pageId;
		}

		// Loads the public page the page
		require_once Configuration::$pagesDirectory . DIRECTORY_SEPARATOR . $pageId . DIRECTORY_SEPARATOR . Configuration::$defaultPageFileName;
		return $pageId;
	}

	/**
	 * Is the page restricted?
	 *
	 * @param string $pageId
	 * @return bool
	 */
	private static function isRestrictedPage($pageId): bool {
		return (new ReflectionClass ( "$pageId\\Page" ))->getMethod ( "isRestricted" )->invoke ( null );
	}
}
?>
