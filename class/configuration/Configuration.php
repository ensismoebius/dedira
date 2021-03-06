<?php
require_once __DIR__ . '/../database/interfaces/IDatabaseDriver.php';
/**
 * Centralizes all system configurations
 *
 * @author André Furlan
 *        
 */
class Configuration {
	
	/**
	 * The of the parameter that, if informed,
	 * informs that the system must generate
	 * an HTML to client application
	 * (generally a browser)
	 * @var integer
	 */
	public static $jsonRequestGetName;
	
	/**
	 * Directory files must be uploaded
	 * @var string
	 */
	public static $uploadPath;
	
	/**
	 * System default charset
	 *
	 * @var string
	 */
	public static $charset;
	
	/**
	 * The host address
	 *
	 * @var string
	 */
	public static $hostAddress;
	
	/**
	 * Default cryptography when sending something using email
	 *
	 * @var string
	 */
	public static $mailCryptography;
	
	/**
	 * Default server port
	 *
	 * @var int
	 */
	public static $mailPort;
	
	/**
	 * Default password when sending something using email
	 *
	 * @var string
	 */
	public static $mailPassword;
	
	/**
	 * Default email protocol
	 *
	 * @var string
	 */
	public static $mailProtocol;
	
	/**
	 * Default username when sending something using email
	 *
	 * @var string
	 */
	public static $mailUsername;
	
	/**
	 * Default email when sending something using email
	 *
	 * @var string
	 */
	public static $mailServer;
	
	/**
	 * Default email when sending something
	 *
	 * @var string
	 */
	public static $mailFrom;
	
	/**
	 * Default name for translantions file
	 *
	 * @var string
	 */
	public static $localeDirName;
	
	/**
	 * The main page name, it should be loaded after authentication
	 *
	 * @var string
	 */
	public static $mainPageName;
	
	/**
	 * The authentication page name, it should be loaded before authentication
	 *
	 * @var string
	 */
	public static $authenticationPageName;
	
	/**
	 * The file name for the page
	 *
	 * @var string
	 */
	public static $pageParameterName;
	
	/**
	 * Database user
	 *
	 * @var string
	 */
	public static $databaseUsername;
	
	/**
	 * Database password
	 * 
	 * @var string
	 */
	public static $databasePassword;
	
	/**
	 * Database name
	 *
	 * @var string
	 */
	public static $databaseNAme;
	
	/**
	 * Database address
	 *
	 * @var string
	 */
	public static $databaseHostAddress;
	
	/**
	 * Database communication protocol
	 *
	 * @var string
	 */
	public static $databaseHostProtocol;
	
	/**
	 * Database communication port
	 *
	 * @var int
	 */
	public static $databasePort;
	
	/**
	 * Path to default css files
	 */
	public static $cssPath;
	
	/**
	 * Site root directory path
	 *
	 * @var string
	 */
	public static $systemRootDirectory;
	
	/**
	 * The default file name for new pages on system
	 *
	 * @var string
	 */
	public static $defaultPageFileName;
	
	/**
	 * The path of the log file
	 *
	 * @var string
	 */
	public static $logFilePath;
	
	/**
	 * The default system language
	 *
	 * @var string
	 */
	public static $defaultLanguage;
	
	/**
	 * Returns the default database driver
	 *
	 * @var IDatabaseDriver
	 */
	public static $databaseDriver;
	
	/**
	 * Indicates where the pages of the system are
	 *
	 * @var string
	 */
	public static $pagesDirectory;
	
	/**
	 * The base template for all pages
	 * here we will put things like menus
	 * headers and stylesheets
	 * @var string
	 */
	public static $rootTemplate;
	
	/**
	 * Initializes the configuration of the system
	 */
	public static function init() {
		
		// Read the ini file
		$arrValues = parse_ini_file ( __DIR__ . "/config.ini", true );
		
		// Auto generated configuration values
		self::$systemRootDirectory = realpath ( dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." );
		self::$logFilePath = self::$systemRootDirectory . DIRECTORY_SEPARATOR . "log" . DIRECTORY_SEPARATOR . "system.log";
		self::$pagesDirectory = self::$systemRootDirectory . DIRECTORY_SEPARATOR . "pages";
		
		// This is mandatory! if some error occurs stop all!
		self::loadDataBaseDriver ( $arrValues ["databaseDriverName"] );
		
		// Default settings (may be override)
		self::$mainPageName = "main";
		self::$defaultLanguage = "pt_BR";
		self::$pageParameterName = "page";
		self::$defaultPageFileName = "Page.php";
		self::$authenticationPageName = "userAuthenticaticator";
		self::$rootTemplate = self::$systemRootDirectory . "/template";
		
		// Read and set the configurations
		$reflection = new ReflectionClass ( "Configuration" );
		$statics = $reflection->getStaticProperties ();
		foreach ( $statics as $name => $value ) {
			
			// If there is no such entry in ini file just ignore and go on
			if (! isset ( $arrValues [$name] ))
				continue;
				// Sets the value from ini file
			$reflection->setStaticPropertyValue ( $name, $arrValues [$name] );
		}
	}
	
	/**
	 * Loads the database driver (this is mandatory!)
	 *
	 * @param string $driver        	
	 * @return IDatabaseDriver
	 */
	private static function loadDataBaseDriver(string $driver) {
		try {
			require_once self::$systemRootDirectory . DIRECTORY_SEPARATOR . "class" . DIRECTORY_SEPARATOR . "database" . DIRECTORY_SEPARATOR . "drivers" . DIRECTORY_SEPARATOR . strtolower ( $driver ) . DIRECTORY_SEPARATOR . $driver . ".php";
			$class = new ReflectionClass ( $driver );
			self::$databaseDriver = $class->newInstance ( null );
		} catch ( Exception $e ) {
			echo Log::recordEntry ( "Fail on load the database driver!" . $e->getMessage () );
			exit ( 2 );
		}
	}
}
?>