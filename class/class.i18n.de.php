<?php
/*	This file is part of CandyDollDB.

CandyDollDB is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

CandyDollDB is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with CandyDollDB.  If not, see <http://www.gnu.org/licenses/>.
*/

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
		'ErrorUpdateTryAgain' => '',
		'ErrorSetupConnectDatabase' => '',
		'ErrorSetupCreatingUser' => '(\'%2$s\'), %1$s',
		'ErrorSetupWritingConfig' => '%1$s',
		'ErrorSetupAlreadyComplete' => '%1$s',
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
		
		'CLIFinished' => 'Fertig.',
		'FooterBy' => '',
		'FooterLoggedInAs' => '',
		'FooterLastLogin' => 'Letzter Besuch',
		'FooterNever' => 'niemals',
		
		'NavigationAdminPanel' => '',
		'NavigationDirtySets' => '',
		'NavigationFeatures' => '',
		'NavigationHome' => '',
		'NavigationImage' => '',
		'NavigationImages' => '',
		'NavigationLogIn' => 'Anmelden',
		'NavigationLogOut' => 'Abmelden',
		'NavigationManageTags' => '',
		'NavigationModel' => '',
		'NavigationModels' => 'Modelle',
		'NavigationMultiDownload' => '',
		'NavigationMyAccount' => '',
		'NavigationNewModel' => 'Neues Model',
		'NavigationProcessXML' => '',
		'NavigationResetYourPassword' => 'Kennwort zurücksetzen',
		'NavigationSearch' => 'Suchen',
		'NavigationSet' => '',
		'NavigationSets' => '',
		'NavigationTagSearch' => 'Tag-Suche',
		'NavigationUsers' => 'Benutzer',
		'NavigationVideo' => '',
		'NavigationVideos' => '',
		
		'LabelAllModels' => '',
		'LabelBirthdate' => 'Geburtsdatum',
		'LabelBirthdateShort' => 'Geboren',
		'LabelBoth' => 'Beide',
		'LabelChecksum' => 'Prüfsumme',
		'LabelClean' => '',
		'LabelCleanCacheFolder' => '',
		'LabelCollection' => 'Sammlung',
		'LabelColorBoxCurrent' => 'Foto {current} von {total}',
		'LabelColorBoxPrevious' => 'Vorheriges Foto',
		'LabelColorBoxNext' => 'Nächstes Foto',
		'LabelColorBoxClose' => 'Schließen',
		'LabelComplete' => 'Komplett',
		'LabelContains' => '',
		'LabelDatabase' => 'Datenbank',
		'LabelDatabaseName' => 'Datenbankname',
		'LabelDate' => 'Datum',
		'LabelDates' => '',
		'LabelDeleteDate' => 'Datum löschen',
		'LabelDeleteImage' => '',
		'LabelDeleteModel' => 'Model löschen',
		'LabelDeleteSelectedTag' => '',
		'LabelDeleteSet' => '',
		'LabelDeleteUser' => 'Benutzer löschen',
		'LabelDeleteVideo' => 'Video löschen',
		'LabelDirty' => '',
		'LabelDownloadImage' => '',
		'LabelDownloadImages' => '',
		'LabelDownloadIndex' => '',
		'LabelDownloadVideo' => '',
		'LabelDownloadVideos' => '',
		'LabelDownloadXML' => '',
		'LabelEditModel' => 'Model bearbeiten',
		'LabelEditSet' => '',
		'LabelEmailAddress' => '',
		'LabelExtension' => 'Dateiendung',
		'LabelFemale' => 'Weiblich',
		'LabelFilename' => 'Dateiname',
		'LabelFilesize' => 'Dateigröße',
		'LabelFirstname' => 'Vorname',
		'LabelFullName' => '',
		'LabelGender' => 'Geslächt',
		'LabelLastname' => '',
		'LabelHeight' => 'Höhe',
		'LabelHostname' => 'Servername',
		'LabelImagesParentheses' => '(Fotos)',
		'LabelIncludeImages' => '',
		'LabelIncludeTags' => '',
		'LabelIncludeVideos' => '',
		'LabelIndexOf' => '',
		'LabelInfo' => 'Info',
		'LabelInsertion' => '',
		'LabelLanguage' => 'Sprache',
		'LabelLanguage_de' => 'Deutsch',
		'LabelLanguage_en' => 'Englisch',
		'LabelLanguage_nl' => 'Niederländisch',
		'LabelLastname' => 'Nachname',
		'LabelLastActive' => '',
		'LabelLastLogin' => '',
		'LabelLastUpdated' => '',
		'LabelMailServer' => 'Mailserver',
		'LabelMale' => 'Männlich',
		'LabelMissingFiles0' => '',
		'LabelMissingFiles1' => '',
		'LabelMissingFilesX' => '',
		'LabelModel' => 'Model',
		'LabelMultiDownloadStep1' => '',
		'LabelMultiDownloadStep2' => '',
		'LabelMultiDownloadStep3' => '',
		'LabelMultiDownloadUseSubfolders' => '',
		'LabelName' => 'Name',
		'LabelNew' => 'Neu',
		'LabelNewPassword' => 'Neues Kennwort',
		'LabelNewUser' => 'Neuer Benutzer',
		'LabelNewVideo' => 'Neues Video',
		'LabelOrphanFiles0' => '',
		'LabelOrphanFiles1' => '',
		'LabelOrphanFilesX' => '',
		'LabelPassword' => 'Kennwort',
		'LabelPasswordGarbage' => 'kEnNw0Rt',
		'LabelPathImages' => 'Foto\'s Pfad',
		'LabelPathVideos' => 'Video\'s Pfad',
		'LabelPathToCandyDollLinux' => '/pfad/zu/candydoll_fotos',
		'LabelPathToCandyDollWin' => 'C:\\Pfad\\Zu\\Candydoll_Fotos',
		'LabelPathToCandyDollVideosLinux' => '/pfad/zu/candydoll_videos',
		'LabelPathToCandyDollVideosWin' => 'C:\\Pfad\\Zu\\Candydoll_Videos',
		'LabelPicSets' => '',
		'LabelPort' => 'Port',
		'LabelPrefix' => '',
		'LabelReEnter' => '',
		'LabelRemarks' => '',
		'LabelRepeatPassword' => '',
		'LabelResultsPerPage' => '',
		'LabelRevisitThisPage' => '',
		'LabelSearchFor' => 'Suchen nach',
		'LabelSelectDateFormat' => '',
		'LabelSelectImageFormat' => '',
		'LabelSenderAddress' => 'Absender (Adresse)',
		'LabelSenderName' => 'Absender (Name)',
		'LabelSetup' => '',
		'LabelShowingXResults' => '<p>%1$s Treffer</p>',
		'LabelSMTPAuth' => '',
		'LabelStartDate' => '',
		'LabelSuffixDefault' => '',
		'LabelSystem' => 'System',
		'LabelTagged' => '',
		'LabelTaggedWith' => '',
		'LabelTags' => '',
		'LabelThumbnails' => '',
		'LabelTitleMr' => '',
		'LabelTitleMrMrs' => '',
		'LabelTitleMrs' => '',
		'LabelTotalImageCount' => '',
		'LabelTotalModelCount' => '',
		'LabelTotalSetCount' => '',
		'LabelTotalTagCount' => '',
		'LabelTotalUserCount' => '',
		'LabelTotalVideoCount' => '',
		'LabelTryAgain' => 'erneut versuchen',
		'LabelUpdateToVersionX' => 'v%1$s',
		'LabelUseMailServer' => 'Verwende Mailserver',
		'LabelUsername' => 'Benutzername',
		'LabelVideosParentheses' => '(Videos)',
		'LabelVidSets' => '',
		'LabelViewImage' => '',
		'LabelViewImages' => '',
		'LabelViewIndexSlideshow' => '',
		'LabelViewModeDetail' => '',
		'LabelViewModeThumbnail' => '',
		'LabelViewVideo' => '',
		'LabelViewVideos' => '',
		'LabelViewSlideshow' => '',
		'LabelWidth' => 'Breite',
		'LabelXtoYofZ' => '%1$d bis %2$d von %3$d',
		
		'ButtonCancel' => 'Abbrechen',
		'ButtonClean' => '',
		'ButtonClearCacheImage' => '',
		'ButtonClearIndexCacheImage' => '',
		'ButtonCreateNewTag' => '',
		'ButtonDelete' => 'Löschen',
		'ButtonDownload' => 'Herunterladen',
		'ButtonGenerate' => 'Generieren',
		'ButtonImportImages' => '',
		'ButtonImportVideos' => '',
		'ButtonIndex' => '',
		'ButtonIndexSlideshow' => '',
		'ButtonLogin' => 'Anmelden',
		'ButtonNewImage' => '',
		'ButtonNewSet' => '',
		'ButtonNext' => 'Weiter',
		'ButtonNoThanks' => 'Nein danke',
		'ButtonPageFirst' => 'Erste Seite',
		'ButtonPageLast' => 'letzte Seite',
		'ButtonPageNext' => 'Nächste Seite',
		'ButtonPagePrevious' => 'Vorherige Seite',
		'ButtonReset' => '',
		'ButtonReturn' => 'Zurück',
		'ButtonSave' => 'Speichern',
		'ButtonSearch' => 'Suchen',
		'ButtonSend' => '',
		'ButtonSetup' => 'Einrichten',
		'ButtonYesPleaseUpdate' => 'Ja, bitte!',

		'MessageAllDoneConfigWritten' => '',
		'MessageForgotYourPassword' => '',
		'MessageSureDeleteDate' => '',
		'MessageCDDBInfo' => "",
		'MessageDataseUpdated' => '',
		'MessageEnjoy' => 'Enjoy!',
		'MessagePasswordReset' => '',
		'MessagePasswordEnterRepeat' => '',
		'MessagePasswordResetError' => '',
		'MessagePasswordResetSuccess' => '',
		'MessagePasswordResetSendError' => '',
		'MessageSureUpdateToX' => 'v%1$s'
	);
}

?>