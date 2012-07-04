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
		'ErrorUpdateTryAgain' => 'Something went wrong while updating, please <a href="%1$s">try again</a>.',
		'ErrorSetupConnectDatabase' => 'Could not connect to the database-server, please %1$s the database-settings.',
		'ErrorSetupCreatingUser' => 'Something went wrong while creating the user (\'%2$s\'), please %1$s.',
		'ErrorSetupWritingConfig' => 'Something went wrong while writing the new config. Please check file permissions and %1$s.',
		'ErrorSetupAlreadyComplete' => 'Setup already complete, please remove \'config.php\' from your installation directory and %1$s.',
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
		
		'NavigationAdminPanel' => 'Admin-panel',
		'NavigationDirtySets' => 'Dirty sets',
		'NavigationFeatures' => 'Features',
		'NavigationHome' => 'Home',
		'NavigationImage' => 'Image',
		'NavigationImages' => 'Images',
		'NavigationLogIn' => 'Login',
		'NavigationLogOut' => 'Logout',
		'NavigationManageTags' => 'Manage tags',
		'NavigationModel' => 'Model',
		'NavigationModels' => 'Models',
		'NavigationMultiDownload' => 'Multi-download',
		'NavigationMyAccount' => 'My account',
		'NavigationNewModel' => 'New model',
		'NavigationProcessXML' => 'Process XML',
		'NavigationResetYourPassword' => 'Reset your password',
		'NavigationSearch' => 'Search',
		'NavigationSet' => 'Set',
		'NavigationSets' => 'Sets',
		'NavigationTagSearch' => 'Tag-search',
		'NavigationUsers' => 'Users',
		'NavigationVideo' => 'Video',
		'NavigationVideos' => 'Videos',
		
		'LabelAllModels' => 'All models',
		'LabelBirthdate' => 'Birthdate',
		'LabelBirthdateShort' => 'Birthdate',
		'LabelBoth' => 'Both',
		'LabelChecksum' => 'Checksum',
		'LabelClean' => 'Clean',
		'LabelCleanCacheFolder' => 'To clean the application\'s cache-folder and cache-table.',
		'LabelCollection' => 'collection',
		'LabelComplete' => 'Complete',
		'LabelContains' => 'Contains',
		'LabelDatabase' => 'Database',
		'LabelDatabaseName' => 'Databasename',
		'LabelDate' => 'Date',
		'LabelDates' => 'Dates',
		'LabelDeleteDate' => 'Delete date',
		'LabelDeleteImage' => 'Delete image',
		'LabelDeleteModel' => 'Delete model',
		'LabelDeleteSelectedTag' => 'Delete selected tag',
		'LabelDeleteSet' => 'Delete set',
		'LabelDeleteUser' => 'Delete user',
		'LabelDirty' => 'Dirty',
		'LabelDownloadImage' => 'Download image',
		'LabelDownloadImages' => 'Download images',
		'LabelDownloadIndex' => 'Download an automatically generated index of a given model. Size is maxed to 1200x1800 pixels.',
		'LabelDownloadVideos' => 'Download videos',
		'LabelDownloadXML' => 'To download an XML-file, based on your own CandyDollDB-collection.',
		'LabelEditModel' => 'Edit model',
		'LabelEditSet' => 'Edit set',
		'LabelEmailAddress' => 'Emailaddress',
		'LabelExtension' => 'Extension',
		'LabelFemale' => 'female',
		'LabelFilename' => 'Filename',
		'LabelFilesize' => 'Filesize',
		'LabelFirstname' => 'Firstname',
		'LabelFullName' => 'Full name',
		'LabelGender' => 'Gender',
		'LabelHeight' => 'Height',
		'LabelHostname' => 'Hostname',
		'LabelImagesParentheses' => '(images)',
		'LabelIncludeImages' => 'Include images',
		'LabelIncludeTags' => 'Include tagged items only (used with include images or video)',
		'LabelIncludeVideos' => 'Include videos',
		'LabelIndexOf' => 'Index of',
		'LabelInfo' => 'Info',
		'LabelInsertion' => 'Middlename',
		'LabelLastname' => 'Lastname',
		'LabelLastActive' => 'Last active',
		'LabelLastLogin' => 'Last login',
		'LabelLastUpdated' => 'Last updated',
		'LabelMailServer' => 'Mailserver',
		'LabelMale' => 'male',
		'LabelMissingFiles0' => 'No missing files',
		'LabelMissingFiles1' => '1 missing file',
		'LabelMissingFilesX' => '%1$d missing files',
		'LabelModel' => 'Model',
		'LabelMultiDownloadStep1' => 'Select one or more models, click Next.',
		'LabelMultiDownloadStep2' => 'Then, select one or more of these models\' sets, click Next.',
		'LabelMultiDownloadStep3' => 'Select one or more images of the selected sets, click Download.',
		'LabelMultiDownloadUseSubfolders' => 'Use subfolders in download',
		'LabelName' => 'Name',
		'LabelNew' => 'New',
		'LabelNewPassword' => 'New password',
		'LabelNewUser' => 'New user',
		'LabelOrphanFiles0' => 'No orphan files',
		'LabelOrphanFiles1' => '1 orphan file',
		'LabelOrphanFilesX' => '%1$d orphan files',
		'LabelPassword' => 'Password',
		'LabelPasswordGarbage' => 'p@s$w0rD',
		'LabelPathImages' => 'Images-path',
		'LabelPathVideos' => 'Videos-path',
		'LabelPathToCandyDollLinux' => '/path/to/candydoll_pics',
		'LabelPathToCandyDollWin' => 'C:\\Path\\To\\Candydoll_pics',
		'LabelPathToCandyDollVideosLinux' => '/path/to/candydoll_vids',
		'LabelPathToCandyDollVideosWin' => 'C:\\Path\\To\\Candydoll_vids',
		'LabelPicSets' => 'Pic-sets',
		'LabelPort' => 'Port',
		'LabelPrefix' => 'Prefix',
		'LabelReEnter' => 're-enter',
		'LabelRemarks' => 'Remarks',
		'LabelRepeatPassword' => 'Repeat password',
		'LabelResultsPerPage' => 'Results per page',
		'LabelRevisitThisPage' => 'revisit this page',
		'LabelSearchFor' => 'Search for',
		'LabelSelectDateFormat' => 'Select date format',
		'LabelSelectImageFormat' => 'Select image format',
		'LabelSenderAddress' => 'Sender (address)',
		'LabelSenderName' => 'Sender (name)',
		'LabelSetup' => 'Setup',
		'LabelShowingXResults' => '<p>Showing %1$s result(s) returned</p>',
		'LabelSMTPAuth' => 'SMTP authentication',
		'LabelStartDate' => 'Start date',
		'LabelSystem' => 'System',
		'LabelTagged' => 'Tagged',
		'LabelTaggedWith' => 'tagged with',
		'LabelTags' => 'Tags',
		'LabelThumbnails' => 'Thumbnails',
		'LabelTitleMr' => 'Mr.',
		'LabelTitleMrMrs' => 'Mr./Mrs.',
		'LabelTitleMrs' => 'Mrs.',
		'LabelTotalImageCount' => 'Total image count',
		'LabelTotalModelCount' => 'Total model count',
		'LabelTotalSetCount' => 'Total set count',
		'LabelTotalTagCount' => 'Total tag count',
		'LabelTotalUserCount' => 'Total user count',
		'LabelTryAgain' => 'try again',
		'LabelUpdateToVersionX' => 'Update to v%1$s',
		'LabelUseMailServer' => 'Use mail server',
		'LabelUsername' => 'Username',
		'LabelVideosParentheses' => '(videos)',
		'LabelVidSets' => 'Vid-sets',
		'LabelViewImage' => 'View image',
		'LabelViewImages' => 'View images',
		'LabelViewIndexSlideshow' => 'View index slideshow',
		'LabelViewSlideshow' => 'View slideshow',
		'LabelViewVideo' => 'View video',
		'LabelViewVideos' => 'View videos',
		'LabelWidth' => 'Width',
		'LabelXtoYofZ' => '%1$d to %2$d of %3$d',

		'ButtonCancel' => 'Cancel',
		'ButtonClean' => 'Clean',
		'ButtonClearCacheImage' => 'Clear cacheimage',
		'ButtonClearIndexCacheImage' => 'Clear index cacheimage',
		'ButtonCreateNewTag' => 'Create new tag',
		'ButtonDelete' => 'Delete',
		'ButtonDownload' => 'Download',
		'ButtonGenerate' => 'Generate',
		'ButtonImportImages' => 'Import images',
		'ButtonImportVideos' => 'Import videos',
		'ButtonIndex' => 'Index',
		'ButtonIndexSlideshow' => 'Index slideshow',
		'ButtonLogin' => 'Login',
		'ButtonNewImage' => 'New image',
		'ButtonNewSet' => 'New set',
		'ButtonNext' => 'Next',
		'ButtonNoThanks' => 'No thanks',
		'ButtonPageFirst' => 'First page',
		'ButtonPageLast' => 'Last page',
		'ButtonPageNext' => 'Next page',
		'ButtonPagePrevious' => 'Previous page',
		'ButtonReset' => 'Reset',
		'ButtonReturn' => 'Return',
		'ButtonSave' => 'Save',
		'ButtonSearch' => 'Search',
		'ButtonSend' => 'Send',
		'ButtonSetup' => 'Setup',
		'ButtonYesPleaseUpdate' => 'Yes, please update',
		
		'MessageAllDoneConfigWritten' => 'All done! Configuration written to \'config.php\'. Please remove this page from the installation and <a href="login.php">log in</a>.',
		'MessageForgotYourPassword' => 'Forgot your password?',
		'MessageSureDeleteDate' => 'Are you sure you wish to delete this date?',
		'MessageCDDBInfo' => "<p>This application is a tribute to the breathtaking beauty of the models shown on <a href=\"http://www.candydoll.tv/\" rel=\"external\">CandyDoll.tv</a> (キャンディドール).</p><p>In CandyDoll's own words:</p>",
		'MessageDataseUpdated' => 'The database has been updated, please <a href="login.php">log-in</a>.',
		'MessageEnjoy' => 'Enjoy!',
		'MessagePasswordReset' => '<p>Please provide the username and e-mailaddress of the account for which you would like to reset the password. A hyperlink will then be sent which will enable you to reset the password.</p>',
		'MessagePasswordEnterRepeat' => '<p>Please provide a new password for your account, and repeat it to avoid typing mistakes. Once your password is reset, you will be loggin in automatically.</p>',
		'MessagePasswordResetError' => '<p>The hypelink you have used is not or no longer valid.<br />Please return to the login-page.</p>',
		'MessagePasswordResetSuccess' => '<p>An e-mail containing a hyperlink has been sent to your e-mailaddress. Use it ito reset your account\'s password.</p>',
		'MessagePasswordResetSendError' => '<p>An error occurred while sending your e-mail. Please contact the system\'s administrator.</p>',
		'MessageSureUpdateToX' => '<p>Are you sure you want to update the application to v%1$s?</p>'
	);
}

?>