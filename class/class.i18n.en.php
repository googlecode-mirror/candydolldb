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
		'ErrorSetupAlreadyComplete' => 'Setup already complete, please remove \'config.php\' from your installation directory and try again.',
		'ErrorSetupConnectDatabase' => 'Could not connect to the database-server, please check the database-settings.',
		'ErrorSetupCreatingCacheDir' => 'Could not find or create the cache-directory.',
		'ErrorSetupCreatingDatabase' => 'Could not create the database.',
		'ErrorSetupCreatingUser' => 'Something went wrong while creating the user.',
		'ErrorSetupWritingConfig' => 'Something went wrong while writing the new config. Please check file permissions.',
		'ErrorSQLErrorTableNotExist' => 'The specified table does not exist.',
		'ErrorSQLErrorUnknown' => 'An unknown SQL error occurred.',
		'ErrorSyntaxEmailAddress' => 'The specified emailaddress is not valid.',
		'ErrorSyntaxErrorUnknown' => 'Unknown syntax error.',
		'ErrorUnknownError' => 'Unknown error.',
		'ErrorUpdateTryAgain' => 'Something went wrong while updating, please try again.',
		'ErrorUploadErrorMaxFilesize' => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
		'ErrorUploadErrorMaxFilesizeIni' => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
		'ErrorUploadErrorMissingTempFolder' => 'Missing a temporary folder.',
		'ErrorUploadErrorNoFile' => 'No file was uploaded.',
		'ErrorUploadErrorOK' => 'There is no error, the file uploaded with success.',
		'ErrorUploadErrorPartialUpload' => 'The uploaded file was only partially uploaded.',
		'ErrorUploadErrorPHPExtension' => 'A PHP extension stopped the file upload.',
		'ErrorUploadErrorUnknown' => 'Unknown upload error.',
		'ErrorUploadErrorWriteToDisk' => 'Failed to write file to disk.',
		'ErrorUserActionNotAllowed' => 'Requested User action not allowed.',
		'ErrorXMLErrorNotValidSchema' => 'The uploaded file failed to validate as CandyDollDB-XML.',
		'ErrorXMLErrorNotValidXML' => 'The uploaded file was not valid XML.',
		'ErrorXMLErrorUnknown' => 'Unknown XML error.',
		
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
		'NavigationImportXML' => 'Import XML',
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
		'LabelAutomatic' => 'Automatic',
		'LabelBirthdate' => 'Birthdate',
		'LabelBirthdateShort' => 'Birthdate',
		'LabelBoth' => 'Both',
		'LabelClean' => 'Clean',
		'LabelCleanCacheFolder' => 'To clean the application\'s cache-folder and cache-table.',
		'LabelCleanUp' => 'Clean-up',
		'LabelCollection' => 'collection',
		'LabelColorBoxClose' => 'Close',
		'LabelColorBoxCurrent' => 'Image {current} of {total}',
		'LabelColorBoxNext' => 'Next',
		'LabelColorBoxPrevious' => 'Previous',
		'LabelCommandlineUser' => 'Commandline user',
		'LabelComplete' => 'Complete',
		'LabelConfiguration' => 'Configuration',
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
		'LabelDeleteVideo' => 'Delete video',
		'LabelDirty' => 'Dirty',
		'LabelDownloadImage' => 'Download image',
		'LabelDownloadImages' => 'Download images',
		'LabelDownloadIndex' => 'Download an automatically generated index of a given model. Size is maxed to 1200x1800 pixels.',
		'LabelDownloadSFV' => 'To download a SFV-file, based on your own CandyDollDB-collection.',
		'LabelDownloadVideo' => 'Download video',
		'LabelDownloadVideos' => 'Download videos',
		'LabelDownloadXML' => 'To download a XML-file, based on your own CandyDollDB-collection.',
		'LabelEditModel' => 'Edit model',
		'LabelEditSet' => 'Edit set',
		'LabelEmailAddress' => 'Emailaddress',
		'LabelExports' => 'Exports',
		'LabelExtension' => 'Extension',
		'LabelFemale' => 'female',
		'LabelFilename' => 'Filename',
		'LabelFilesize' => 'Filesize',
		'LabelFirstAppearance' => 'First set date',
		'LabelFirstname' => 'Firstname',
		'LabelFullName' => 'Full name',
		'LabelGender' => 'Gender',
		'LabelHeight' => 'Height',
		'LabelHostname' => 'Hostname',
		'LabelImagesParentheses' => '(images)',
		'LabelIncludeImages' => 'Include images',
		'LabelIncludePath' => 'Include full path',
		'LabelIncludeTags' => 'Include tagged items only (used with include images or video)',
		'LabelIncludeVideos' => 'Include videos',
		'LabelIndexOf' => 'Index of',
		'LabelInfo' => 'Info',
		'LabelInsertion' => 'Middlename',
		'LabelLanguage' => 'Language',
		'LabelLanguage_de' => 'German',
		'LabelLanguage_en' => 'English',
		'LabelLanguage_nl' => 'Dutch',
		'LabelLastActive' => 'Last active',
		'LabelLastAppearance' => 'Last set date',
		'LabelLastLogin' => 'Last login',
		'LabelLastname' => 'Lastname',
		'LabelLastUpdated' => 'Last updated',
		'LabelMailServer' => 'Mailserver',
		'LabelMale' => 'male',
		'LabelMD5Checksum' => 'MD5-checksum',
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
		'LabelNewVideo' => 'New video',
		'LabelNotAllowed' => 'User action not allowed',
		'LabelOrphanFiles0' => 'No orphan files',
		'LabelOrphanFiles1' => '1 orphan file',
		'LabelOrphanFilesX' => '%1$d orphan files',
		'LabelPassword' => 'Password',
		'LabelPasswordGarbage' => 'p@s$w0rD',
		'LabelPathOnDisk' => 'Path on disk',
		'LabelPathToCandyDollLinux' => '/path/to/candydoll',
		'LabelPathToCandyDollWin' => 'C:\\Path\\To\\Candydoll',
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
		'LabelSetCount' => 'Set count',
		'LabelSetup' => 'Setup',
		'LabelSFVPathFull' => 'Absolute filenames',
		'LabelSFVPathNone' => 'Filenames only',
		'LabelSFVPathRelative' => 'Relative filenames',
		'LabelShowingXResults' => '<p>Showing %1$s result(s) returned</p>',
		'LabelSMTPAuth' => 'SMTP authentication',
		'LabelSorting' => 'Sorting',
		'LabelSortingASC' => 'Ascending',
		'LabelSortingDESC' => 'Descending',
		'LabelStartDate' => 'Start date',
		'LabelSuffixDefault' => ' [default]',
		'LabelSystem' => 'System',
		'LabelTagged' => 'Tagged',
		'LabelTaggedWith' => 'tagged with',
		'LabelTags' => 'Tags',
		'LabelThumbnails' => 'Thumbnails',
		'LabelThumbnailsPerPage' => 'Thumbnails per page',
		'LabelTitleMr' => 'Mr.',
		'LabelTitleMrMrs' => 'Mr./Mrs.',
		'LabelTitleMrs' => 'Mrs.',
		'LabelTotalImageCount' => 'Total image count',
		'LabelTotalModelCount' => 'Total model count',
		'LabelTotalSetCount' => 'Total set count',
		'LabelTotalTagCount' => 'Total tag count',
		'LabelTotalUserCount' => 'Total user count',
		'LabelTotalVideoCount' => 'Total video count',
		'LabelTryAgain' => 'try again',
		'LabelUpdateToVersionX' => 'Update to v%1$s',
		'LabelUseMailServer' => 'Use mail server',
		'LabelUsername' => 'Username',
		'LabelUserRights' => 'User rights',
		'LabelVideosParentheses' => '(videos)',
		'LabelVidSets' => 'Vid-sets',
		'LabelViewImage' => 'View image',
		'LabelViewImages' => 'View images',
		'LabelViewIndexSlideshow' => 'View index slideshow',
		'LabelViewModeDetail' => 'Detail view',
		'LabelViewModeThumbnail' => 'Thumbnail view',
		'LabelViewSlideshow' => 'View slideshow',
		'LabelViewVideo' => 'View video',
		'LabelViewVideos' => 'View videos',
		'LabelWidth' => 'Width',
		'LabelXMLFile' => 'XML-file',
		'LabelXtoYofZ' => '%1$d to %2$d of %3$d',
		
		'LabelRIGHT_ACCOUNT_EDIT' => 'Edit account settings',
		'LabelRIGHT_ACCOUNT_LOGIN' => 'Login',
		'LabelRIGHT_ACCOUNT_PASSWORD' => 'Edit password',
		'LabelRIGHT_CACHE_CLEANUP' => 'Clean-up cache',
		'LabelRIGHT_CACHE_DELETE' => 'Delete cached image',
		'LabelRIGHT_CONFIG_REWRITE' => 'Edit configuration',
		'LabelRIGHT_EXPORT_CSV' => 'Export CSV',
		'LabelRIGHT_EXPORT_INDEX' => 'Downwload model-index',
		'LabelRIGHT_EXPORT_SFV' => 'Export SFV',
		'LabelRIGHT_EXPORT_VIDEO' => 'Downwload video',
		'LabelRIGHT_EXPORT_XML' => 'Export XML',
		'LabelRIGHT_EXPORT_ZIP' => 'Download (zip)',
		'LabelRIGHT_EXPORT_ZIP_MULTI' => 'Multi-download (zip)',
		'LabelRIGHT_IMAGE_ADD' => 'Add new image',
		'LabelRIGHT_IMAGE_DELETE' => 'Delete image',
		'LabelRIGHT_IMAGE_EDIT' => 'Edit image',
		'LabelRIGHT_IMPORT_XML' => 'Import XML',
		'LabelRIGHT_MODEL_ADD' => 'Add new model',
		'LabelRIGHT_MODEL_DELETE' => 'Delete model',
		'LabelRIGHT_MODEL_EDIT' => 'Edit model',
		'LabelRIGHT_SEARCH' => 'Search',
		'LabelRIGHT_SEARCH_DIRTY' => 'Search dirty sets',
		'LabelRIGHT_SEARCH_TAGS' => 'Search tags',
		'LabelRIGHT_SET_ADD' => 'Add new set',
		'LabelRIGHT_SET_DELETE' => 'Delete set',
		'LabelRIGHT_SET_EDIT' => 'Edit set',
		'LabelRIGHT_TAG_ADD' => 'Add new tag',
		'LabelRIGHT_TAG_CLEANUP' => 'Clean-up tags',
		'LabelRIGHT_TAG_DELETE' => 'Delete tag',
		'LabelRIGHT_TAG_EDIT' => 'Edit tag',
		'LabelRIGHT_USER_ADD' => 'Add new user',
		'LabelRIGHT_USER_DELETE' => 'Delete user',
		'LabelRIGHT_USER_EDIT' => 'Edit user',
		'LabelRIGHT_USER_RIGHTS' => 'Manage user rights',
		'LabelRIGHT_VIDEO_ADD' => 'Add new video',
		'LabelRIGHT_VIDEO_DELETE' => 'Delete video',
		'LabelRIGHT_VIDEO_EDIT' => 'Edit video',
		
		'ButtonCancel' => 'Cancel',
		'ButtonClean' => 'Clean',
		'ButtonClearCacheImage' => 'Clear cacheimage',
		'ButtonClearCacheImages' => 'Clear cacheimages',
		'ButtonClearIndexCacheImage' => 'Clear index cacheimage',
		'ButtonCreateNewTag' => 'Create new tag',
		'ButtonDelete' => 'Delete',
		'ButtonDownload' => 'Download',
		'ButtonGenerate' => 'Generate',
		'ButtonImportImages' => 'Import images',
		'ButtonImportVideos' => 'Import videos',
		'ButtonImportXML' => 'Import XML',
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
		'ButtonToggle' => 'Toggle selection',
		'ButtonYesPleaseUpdate' => 'Yes, please update',
		
		'MessageAllDoneConfigWritten' => 'All done! Configuration written to \'config.php\'. Please remove the setup-page from the installation and log in.',
		'MessageCacheImagesCleaned' => 'Both cacheimage folder and table were cleaned successfully.',
		'MessageCDDBInfo' => "<p>This application is a tribute to the breathtaking beauty of the models shown on <a href=\"http://www.candydoll.tv/\" rel=\"external\">CandyDoll.tv</a> (キャンディドール).</p><p>In CandyDoll's own words:</p>",
		'MessageConfigWritten' => 'Configuration written to \'config.php\' successfully.',
		'MessageDataseUpdated' => 'The database has been updated, please remove the setup-files and log-in.',
		'MessageEnjoy' => 'Enjoy!',
		'MessageForgotYourPassword' => 'Forgot your password?',
		'MessageImagesImported' => 'The pictures have been imported successfully.',
		'MessagePasswordEnterRepeat' => '<p>Please provide a new password for your account, and repeat it to avoid typing mistakes. Once your password is reset, you will be loggin in automatically.</p>',
		'MessagePasswordReset' => '<p>Please provide the username and e-mailaddress of the account for which you would like to reset the password. A hyperlink will then be sent which will enable you to reset the password.</p>',
		'MessagePasswordResetError' => '<p>The hypelink you have used is not or no longer valid.<br />Please return to the login-page.</p>',
		'MessagePasswordResetSendError' => '<p>An error occurred while sending your e-mail. Please contact the system\'s administrator.</p>',
		'MessagePasswordResetSuccess' => '<p>An e-mail containing a hyperlink has been sent to your e-mailaddress. Use it ito reset your account\'s password.</p>',
		'MessageSureDeleteDate' => 'Are you sure you wish to delete this date?',
		'MessageSureUpdateToX' => '<p>Are you sure you want to update the application to v%1$s?</p>',
		'MessageTagsCleaned' => 'The tag collection has been cleaned successfully.',
		'MessageVideosImported' => 'The videos have been imported successfully.',
		'MessageXMLImported' => 'The XML data has been imported successfully.'
	);
}

?>