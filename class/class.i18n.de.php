<?php

class LabelsDE extends Labels
{
	public static function LabelExists($key){
		return array_key_exists($key, self::$Labels) && strlen(self::$Labels[$key]) > 0;
	}
	
	/**
	 * All user readable strings used in the application, in German 
	 * @var array
	 */
	public static $Labels = array(
		'ErrorLoginErrorHyperlinkInvalid' => '',
		'ErrorLoginErrorPasswordIncorect' => '',
		'ErrorLoginErrorPasswordsNotIdentical' => '',
		'ErrorLoginErrorUnknown' => '',
		'ErrorLoginErrorUsernameEmailCombo' => '',
		'ErrorLoginErrorUsernameNotFound' => '',
		'ErrorNotAllRequiredData' => '',
		'ErrorPleaseUseWebInterfaceForSetup' => 'Benutzen Sie bitte das Webformular zur Einrichtung dieses Programms.',
		'ErrorSQLErrorTableNotExist' => '',
		'ErrorSQLErrorUnknown' => '',
		'ErrorSyntaxEmailAddress' => '',
		'ErrorSyntaxErrorUnknown' => '',
		'ErrorUnknownError' => '',
		'ErrorUploadErrorMaxFilesize' => '',
		'ErrorUploadErrorMaxFilesizeIni' => '',
		'ErrorUploadErrorMissingTempFolder' => '',
		'ErrorUploadErrorNoFile' => '',
		'ErrorUploadErrorOK' => '',
		'ErrorUploadErrorPartialUpload' => '',
		'ErrorUploadErrorPHPExtension' => '',
		'ErrorUploadErrorUnknown' => '',
		'ErrorUploadErrorWriteToDisk' => '',
		'ErrorUploadErrorWriteToDisk' => ''
	);
}

?>