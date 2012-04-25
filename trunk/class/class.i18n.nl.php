<?php

class LabelsNL extends Labels
{
	public static function LabelExists($key){
		return array_key_exists($key, self::$Labels) && strlen(self::$Labels[$key]) > 0;
	}
	
	/**
	 * All user readable strings used in the application, in Dutch 
	 * @var array
	 */
	public static $Labels = array(
		'ErrorLoginErrorHyperlinkInvalid' => 'De gevolgde snelkoppeling is niet of niet langer geldig.',
		'ErrorLoginErrorPasswordIncorect' => 'Het wachtwoord is niet correct.',
		'ErrorLoginErrorPasswordsNotIdentical' => 'De wachtwoorden komen niet met elkaar overeen.',
		'ErrorLoginErrorUnknown' => 'Onbekende login-fout.',
		'ErrorLoginErrorUsernameEmailCombo' => 'De combinatie van gebruikersnaam en emailadres werd niet gevonden.',
		'ErrorLoginErrorUsernameNotFound' => 'De opgegeven gebruikersnaam werd niet gevonden.',
		'ErrorNotAllRequiredData' => 'Niet alle verplichte velden zijn ingevuld.',
		'ErrorPleaseUseWebInterfaceForSetup' => 'Maak alstublieft gebruik van de web-interface om deze applicatie in te richten.',
		'ErrorSQLErrorTableNotExist' => 'De opgegeven tabel bestaat niet.',
		'ErrorSQLErrorUnknown' => 'Er is een onbekende SQL fout opgetreden.',
		'ErrorSyntaxEmailAddress' => 'Het opgegeven emailadres is niet geldig.',
		'ErrorSyntaxErrorUnknown' => 'Onkenede formaat-fout.',
		'ErrorUnknownError' => 'Onbekende fout.',
		'ErrorUploadErrorMaxFilesize' => 'Het bestand is groter dan toegestaan in MAX_FILE_SIZE.',
		'ErrorUploadErrorMaxFilesizeIni' => 'Het bestand is groter dan toegestaan in php.ini.',
		'ErrorUploadErrorMissingTempFolder' => 'De tijdelijke map voor het opslaan ontbreekt.',
		'ErrorUploadErrorNoFile' => 'Geen bestand geüpload.',
		'ErrorUploadErrorOK' => 'Er is geen fout opgetreden tijdens het uploaden.',
		'ErrorUploadErrorPartialUpload' => 'Het bestand is niet volledig opgeslagen.',
		'ErrorUploadErrorPHPExtension' => 'Een geïnstalleerde PHP extensie heeft het uploaden gestopt.',
		'ErrorUploadErrorUnknown' => 'Onbekende upload-fout.',
		'ErrorUploadErrorWriteToDisk' => 'Het bestand kon niet worden weggeschreven.',
		
		'CLIFinished' => 'Finished.',
		'FooterBy' => 'door',
		'FooterLoggedInAs' => 'Aangemeld als',
		'FooterLastLogin' => 'Laatste bezoek',
		'FooterNever' => 'nooit',
		
		'NavigationHome' => 'Home',
		'NavigationFeatures' => 'Onderdelen',
		'NavigationProcessXML' => 'XML verwerken',
		'NavigationNewModel' => 'Nieuw model',
		'NavigationManageTags' => 'Tags beheren',
		'NavigationAdminPanel' => 'Admin-pagina',
		'NavigationMultiDownload' => 'Multi-download',
		'NavigationUsers' => 'Gebruikers',
		'NavigationMyAccount' => 'Mijn account',
		'NavigationSearch' => 'Zoeken',
		'NavigationTagSearch' => 'Tags zoeken',
		'NavigationDirtySets' => 'Incomplete sets',
		'NavigationLogOut' => 'Afmelden',
		'NavigationModels' => 'Modellen',
		'NavigationModel' => 'Model',
		'NavigationSets' => 'Sets',
		'NavigationSet' => 'Set',
		'NavigationImages' => 'Foto\'s',
		'NavigationImage' => 'Foto',
		'NavigationVideos' => 'Video\'s',
		'NavigationVideo' => 'Video',
		
		'LabelDate' => 'Datum',
		'LabelImagesParentheses' => '(foto\'s)',
		'LabelVideosParentheses' => '(video\'s)',
		'LabelDeleteDate' => 'Datum verwijderen',
		'LabelTitleMrMrs' => 'Dhr./Mevr.',
		'LabelTitleMr' => 'Dhr.',
		'LabelTitleMrs' => 'Mevr.',
		'LabelOrphanFiles0' => 'Geen loze bestanden',
		'LabelOrphanFiles1' => '1 loos bestand',
		'LabelOrphanFilesX' => '%1$d loze bestanden',
		'LabelMissingFiles0' => 'Geen ontbrekende bestanden',
		'LabelMissingFiles1' => '1 ontbrekend bestand',
		'LabelMissingFilesX' => '%1$d ontbrekende bestanden',
		'LabelCleanCacheFolder' => 'De map met cache-bestanden en de cache-tabel opruimen.',
		'LabelDownloadXML' => 'Een XML-bestand downloaden, gebaseerd op de eigen CandyDollDB-collectie.',
		'LabelDownloadIndex' => 'Een automatisch gegenereerde index downloaden van maximaal 1200x1800 pixels.',
		'LabelModel' => 'Model',
		'LabelAllModels' => 'Alle modellen',
		'LabelIncludeImages' => 'Inclusief foto\'s',
		'LabelIncludeVideos' => 'Inclusief video\'s',
		'LabelWidth' => 'Breedte',
		'LabelHeight' => 'Hoogte',
		'LabelMultiDownloadUseSubfolders' => 'Submappen in download',
		'LabelMultiDownloadStep1' => 'Selecteer één of meer modellen, klik volgende.',
		'LabelMultiDownloadStep2' => 'Selecteer één of meer van de getoonde sets, klik volgende.',
		'LabelMultiDownloadStep3' => 'Selecteer één of meer van de getoonde foto\'s, klik Download.',
		'LabelNew' => 'Nieuw',
		'LabelTags' => 'Tags',
		'LabelChecksum' => 'Controlecijfer',
		'LabelFilesize' => 'Grootte',
		'LabelExtension' => 'Extensie',
		'LabelFilename' => 'Bestandsnaam',
		'LabelEditModel' => 'Model bewerken',
		'LabelDownloadImage' => 'Download foto',
		'LabelDeleteImage' => 'Foto verwijderen',
		'LabelViewImage' => 'Foto bekijken',
		'LabelTotalImageCount' => 'Totaal aantal foto\'s',
		'LabelViewSlideshow' => 'Diashow bekijken',
		
		'ButtonClean' => 'Opruimen',
		'ButtonNext' => 'Volgende',
		'ButtonDownload' => 'Download',
		'ButtonReset' => 'Reset',
		'ButtonCancel' => 'Annuleren',
		'ButtonSave' => 'Opslaan',
		'ButtonDelete' => 'Verwijderen',
		'ButtonClearCacheImage' => 'Cachefoto verwijderen',
		'ButtonNewImage' => 'Nieuwe foto',
		'ButtonImportImages' => 'Foto\'s importeren',
		
		'MessageSureDeleteDate' => 'Weet u zeker dat u deze datum wil verwijderen?'
	);
}

?>