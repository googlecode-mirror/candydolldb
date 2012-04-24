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
		'ErrorUploadErrorWriteToDisk' => '',
		
		'CLIFinished' => 'Fertig.',
		'FooterBy' => '',
		'FooterLoggedInAs' => '',
		'FooterLastLogin' => 'Letzter Besuch',
		'FooterNever' => 'niemals',
		
		'NavigationHome' => '',
		'NavigationFeatures' => '',
		'NavigationProcessXML' => '',
		'NavigationNewModel' => 'Neues Model',
		'NavigationManageTags' => '',
		'NavigationAdminPanel' => '',
		'NavigationMultiDownload' => '',
		'NavigationUsers' => 'Benutzer',
		'NavigationMyAccount' => '',
		'NavigationSearch' => 'Suchen',
		'NavigationTagSearch' => 'Tag-Suche',
		'NavigationDirtySets' => '',
		'NavigationLogOut' => 'Abmelden',
		
		'LabelDate' => 'Datum',
		'LabelImagesParentheses' => '(Fotos)',
		'LabelVideosParentheses' => '(Videos)',
		'LabelDeleteDate' => 'Datum löschen',
		'LabelTitleMrMrs' => '',
		'LabelTitleMr' => '',
		'LabelTitleMrs' => '',
		'LabelOrphanFiles0' => '',
		'LabelOrphanFiles1' => '',
		'LabelOrphanFilesX' => '',
		'LabelMissingFiles0' => '',
		'LabelMissingFiles1' => '',
		'LabelMissingFilesX' => '',
		'LabelCleanCacheFolder' => '',
		'LabelDownloadXML' => '',
		'LabelDownloadIndex' => '',
		'LabelModel' => 'Model',
		'LabelAllModels' => '',
		'LabelIncludeImages' => '',
		'LabelIncludeVideos' => '',
		'LabelWidth' => 'Breite',
		'LabelHeight' => 'Höhe',
		
		'ButtonClean' => '',
		'ButtonDownload' => 'Herunterladen',
		
		'MessageSureDeleteDate' => ''
	);
}

?>