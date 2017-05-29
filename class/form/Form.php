<?php
require_once __DIR__ . '/../variable/Caster.php';
require_once __DIR__ . '/../filesystem/File.php';
/**
 * Retrives fields from form and return a desired object from it
 *
 * @author ensismoebius
 *        
 */
final class Form {
	
	/**
	 * The prefix uploaded file has to has
	 *
	 * @var string
	 */
	private $uploadedFilePrefix;
	
	/**
	 * Holds the destiny of files send by a form
	 * it must be informed when uploading some file
	 *
	 * @var string
	 */
	private $pathForFileUpload;
	
	/**
	 * Holds the filters for the properties
	 *
	 * @var array
	 */
	private $arrFilters;
	
	/**
	 * Holds the target object form must generate
	 *
	 * @var object
	 */
	private $targetObject;
	
	/**
	 * Holds the generated object
	 *
	 * @var object
	 */
	private $generatedObject;
	
	/**
	 * the source of data you will retrieve from
	 *
	 * @var integer
	 */
	private $getOrPost;
	
	/**
	 * the source of data you will retrieve from
	 *
	 * @var integer
	 */
	const TYPE_GET = 0;
	
	/**
	 * the source of data you will retrieve from
	 *
	 * @var integer
	 */
	const TYPE_POST = 1;
	
	/**
	 * path for file upload
	 *
	 * @param string $pathForFileUpload        	
	 * @return Form
	 */
	public function setPathForFileUpload($pathForFileUpload) {
		$this->pathForFileUpload = $pathForFileUpload;
		return $this;
	}
	
	/**
	 * Informs the source of data you will retrieve from
	 *
	 * @param int $getOrPost        	
	 * @return Form
	 */
	public function setType($getOrPost): Form {
		$this->getOrPost = $getOrPost;
		return $this;
	}
	
	/**
	 * Sets an instace of the target object the form must generate
	 *
	 * @param object $object        	
	 * @return Form
	 */
	public function setTargetObject($object): Form {
		$this->targetObject = $object;
		return $this;
	}
	
	/**
	 * Generates the object from form data
	 *
	 * @return boolean
	 */
	public function generateObject() {
		$dataSource = null;
		
		// You must set the source of data you will retrieve from
		switch ($this->getOrPost) {
			case self::TYPE_GET :
				$generatedObject = $this->validateAndSanitize ( $this->arrFilters, $_GET );
				break;
			
			case self::TYPE_POST :
				// Sanitize data and generate the object
				$generatedObject = $this->validateAndSanitize ( $this->arrFilters, $_POST, $_FILES );
				break;
			
			default :
				return false;
		}
		
		// If object is a boolean something goes wrong
		if (is_bool ( $generatedObject )) {
			return false;
		}
		
		// Sets the generated object
		$this->generatedObject = $generatedObject;
		return true;
	}
	
	/**
	 * Register the form fields that must be filtered
	 *
	 * @param string $fieldName        	
	 * @param integer $filterType        	
	 */
	public function registerField(string $fieldName, int $filterType, bool $mandatory = true) {
		$this->arrFilters [$mandatory] [$fieldName] = $filterType;
	}
	
	/**
	 * Sanitizes fields and generate object
	 *
	 * @param array $arrFiltersAndFieldNames        	
	 * @param array $dataSource        	
	 * @return boolean|object
	 */
	private function validateAndSanitize($arrAllData, &$dataSource, &$fileDataSource = null) {
		
		// We start manipulating the default datasource ($_POS or $_GET)
		$manipulatedDataSource = &$dataSource;
		
		// Iterate over mandatory / non mandatory data
		foreach ( $arrAllData as $isMandatory => $arrFiltersAndFieldNames ) {
			
			// Iterates over filters
			foreach ( $arrFiltersAndFieldNames as $fieldName => $filterType ) {
				
				if ($isMandatory) {
					// Choosing the source of the data for mandatory fields
					
					if (isset ( $dataSource [$fieldName] )) {
						// There is a field in the default datasource
						$manipulatedDataSource = &$dataSource;
					} elseif (isset ( $fileDataSource [$fieldName] )) {
						
						// Im sending files too
						$manipulatedDataSource = &$fileDataSource;
						
						$file = new File ();
						$file->setCaminhoDoArquivo ( $fileDataSource [$fieldName] ['name'], false );
						
						$filename = $this->renameAndMoveFile ( $fileDataSource [$fieldName] ['tmp_name'], $file->getFileExtension () );
						
						if (is_bool ( $filename )) {
							return false;
						}
						
						// Updates the file name
						$fileDataSource [$fieldName] = $filename;
					} else {
						// I send nothing
						return false;
					}
				} else {
					
					// Choosing the source of the data for non mandatory fields
					
					if (isset ( $dataSource [$fieldName] )) {
						// There is a field in the default datasource
						$manipulatedDataSource = &$dataSource;
					} elseif (isset ( $fileDataSource [$fieldName] )) {
						// Im sending files too
						$manipulatedDataSource = &$fileDataSource;
						
						$file = new File ();
						$file->setCaminhoDoArquivo ( $fileDataSource [$fieldName] ['name'] );
						
						$filename = $this->renameAndMoveFile ( $fileDataSource [$fieldName] ['tmp_name'], $file->getFileExtension () );
						
						if (is_bool ( $filename )) {
							return false;
						}
						
						// Updates the file name
						$fileDataSource [$fieldName] = $file;
					}
					
					// Well... we find nothing, but the field is not mandatory anyway...
					$manipulatedDataSource = &$dataSource;
				}
				
				if (is_array ( $manipulatedDataSource [$fieldName] )) {
					
					foreach ( $manipulatedDataSource [$fieldName] as &$value ) {
						$result = filter_var ( trim ( $value ), $filterType );
						
						// If result is boolean the filtering has failed
						// So stops everything and return false
						if (is_bool ( $result )) {
							return false;
						}
						
						// Filter is ok, go on
						$value = $result;
					}
					continue;
				}
				
				$result = filter_var ( trim ( $manipulatedDataSource [$fieldName] ), $filterType );
				
				// If result is boolean the filtering has failed
				// So stops everything and return false
				if (is_bool ( $result )) {
					return false;
				}
				
				// Filter is ok, go on
				$manipulatedDataSource [$fieldName] = $result;
			}
		}
		
		$dataSource = array_merge ( $dataSource, $fileDataSource );
		
		return Caster::arrayToClassCast ( $dataSource, $this->targetObject );
	}
	
	// Rename and move file
	public function renameAndMoveFile(string $filename, string $extension) {
		
		// The path MUST be set
		if ($this->pathForFileUpload == "") {
			return false;
		}
		
		$finalFileName = $this->uploadedFilePrefix . "." . microtime ( true ) . "." . $extension;
		
		try {
			move_uploaded_file ( $filename, $this->pathForFileUpload . DIRECTORY_SEPARATOR . $finalFileName );
			return $finalFileName;
		} catch ( Exception $e ) {
			// The file naming and creation cant goes wrong
			return false;
		}
	}
	
	/**
	 * Returns the generated object
	 *
	 * @return object
	 */
	public function getObject() {
		return $this->generatedObject;
	}
	
	/**
	 * uploaded file prefix
	 *
	 * @param string $uploadedFilePrefix        	
	 * @return Form
	 */
	public function setUploadedFilePrefix($uploadedFilePrefix) {
		$this->uploadedFilePrefix = trim ( $uploadedFilePrefix );
		return $this;
	}
}
?>

