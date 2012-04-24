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
		'ErrorUploadErrorWriteToDisk' => 'Failed to write file to disk.',
		
		'CLIFinished' => 'Finished.',
		'FooterBy' => 'by',
		'FooterLoggedInAs' => 'Logged in as',
		'FooterLastLogin' => 'Last login',
		'FooterNever' => 'never',
		
		'NavigationHome' => 'Home',
		'NavigationFeatures' => 'Features',
		'NavigationProcessXML' => 'Process XML',
		'NavigationNewModel' => 'New model',
		'NavigationManageTags' => 'Manage tags',
		'NavigationAdminPanel' => 'Admin-panel',
		'NavigationMultiDownload' => 'Multi-download',
		'NavigationUsers' => 'Users',
		'NavigationMyAccount' => 'My account',
		'NavigationSearch' => 'Search',
		'NavigationTagSearch' => 'Tag-search',
		'NavigationDirtySets' => 'Dirty sets',
		'NavigationLogOut' => 'Logout',
		
		'LabelDate' => 'Date',
		'LabelImagesParentheses' => '(images)',
		'LabelVideosParentheses' => '(videos)',
		'LabelDeleteDate' => 'Delete date',
		'LabelTitleMrMrs' => 'Mr./Mrs.',
		'LabelTitleMr' => 'Mr.',
		'LabelTitleMrs' => 'Mrs.',
		'LabelOrphanFiles0' => 'No orphan files',
		'LabelOrphanFiles1' => '1 orphan file',
		'LabelOrphanFilesX' => '%1$d orphan files',
		'LabelMissingFiles0' => 'No missing files',
		'LabelMissingFiles1' => '1 missing file',
		'LabelMissingFilesX' => '%1$d missing files',
		'LabelCleanCacheFolder' => 'To clean the application\'s cache-folder and cache-table.',
		'LabelDownloadXML' => 'To download an XML-file, based on your own CandyDollDB-collection.',
		'LabelDownloadIndex' => 'Download an automatically generated index of a given model. Size is maxed to 1200x1800 pixels.',
		'LabelModel' => 'Model',
		'LabelAllModels' => 'All models',
		'LabelIncludeImages' => 'Include images',
		'LabelIncludeVideos' => 'Include videos',
		'LabelWidth' => 'Width',
		'LabelHeight' => 'Height',

		'ButtonClean' => 'Clean',
		'ButtonDownload' => 'Download',
		
		'MessageSureDeleteDate' => 'Are you sure you wish to delete this date?'
	);
}

?>