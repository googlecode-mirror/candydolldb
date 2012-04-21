<?php

class LabelsEN extends Labels
{
	public static function LabelExists($key){
		return array_key_exists($key, self::$Labels) && strlen(self::$Labels[$key]) > 0;
	}
	
	/**
	 * All user readable strings used in the application, in English 
	 * @var array(string)
	 */
	public static $Labels = array(
		'ErrorLoginErrorHyperlinkInvalid' => 'The hypelink you have used is not or no longer valid.',
		'ErrorLoginErrorPasswordIncorect' => 'The specified password is not correct.',
		'ErrorLoginErrorPasswordsNotIdentical' => 'The provided passwords are not identical.',
		'ErrorLoginErrorUnknown' => 'Unknown login error.',
		'ErrorLoginErrorUsernameEmailCombo' => 'The specified combination of username and e-mailaddress was not found.',
		'ErrorLoginErrorUsernameNotFound' => 'The specified username was not found.',
		'ErrorNotAllRequiredData' => 'Not all required data was entered.',
		'ErrorPleaseUseWebInterfaceForSetup' => 'Please use the webinterface for setting up this application.',
		'ErrorSQLErrorTableNotExist' => 'The specified table does not exist.',
		'ErrorSQLErrorUnknown' => 'An unknown SQL error occurred.',
		'ErrorSyntaxEmailAddress' => 'The specified emailaddress is not valid.',
		'ErrorSyntaxErrorUnknown' => 'Unknown syntax error.',
		'ErrorUnknownError' => 'Unknown error.',
		'ErrorUploadErrorMaxFilesize' => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
		'ErrorUploadErrorMaxFilesizeIni' => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
		'ErrorUploadErrorMissingTempFolder' => 'Missing a temporary folder.',
		'ErrorUploadErrorNoFile' => 'No file was uploaded.',
		'ErrorUploadErrorOK' => 'There is no error, the file uploaded with success.',
		'ErrorUploadErrorPartialUpload' => 'The uploaded file was only partially uploaded.',
		'ErrorUploadErrorPHPExtension' => 'A PHP extension stopped the file upload.',
		'ErrorUploadErrorUnknown' => 'Unknown upload error.',
		'ErrorUploadErrorWriteToDisk' => 'Failed to write file to disk.'
	);
}

?>