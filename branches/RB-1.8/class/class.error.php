<?php

class Error
{
	private $ErrorNumber;
	
	/**
	 * return int
	 */
	public function getErrorNumber()
	{ return $this->ErrorNumber; }
	
	/**
	 * Sets this Error's number and translates it to a corresponding message.
	 * @param int $InErrorNumber
	 */
	public function setErrorNumber($InErrorNumber)
	{
		$this->ErrorNumber = $InErrorNumber;
		$this->ErrorMessage = static::TranslateError($InErrorNumber);
	}
	
	private $ErrorMessage;
	
	/**
	 * return string
	 */
	public function getErrorMessage()
	{ return $this->ErrorMessage; }
	
	/**
	 * Sets this Error's message.
	 * @param string $InErrorMessage
	 */
	public function setErrorMessage($InErrorMessage)
	{ $this->ErrorMessage = $InErrorMessage; }
	
	public function __construct($ErrorNumber = NULL, $ErrorMessage = NULL)
	{
		$this->ErrorNumber = $ErrorNumber;
		$this->ErrorMessage = ($ErrorMessage ? $ErrorMessage : static::TranslateError($ErrorNumber));
	}
	
	/**
	 * Adds an error to the $_SESSION error-array, or outputs it to STERR.
	 * @param Error $InError
	 */
	public static function AddError($InError)
	{
		global $argv, $argc;
		
		if(isset($argv) && $argc > 0)
		{
			fwrite(STDERR, html_entity_decode($InError->getErrorMessage(), ENT_COMPAT, 'UTF-8')."\n");
		}
		else 
		{
			$Errors = unserialize($_SESSION['Errors']);
			$Errors[] = $InError;
			$_SESSION['Errors'] = serialize($Errors);
		}
	}
	
	/**
	 * Generates a HTML unordered list, suitable for showing in an errorscreen overlay.
	 * @return string|NULL
	 */
	public static function GenerateErrorList()
	{
		$errorList = '';
		$errorCount = 0;
		$inErrors = unserialize($_SESSION['Errors']);
	
		if(is_array($inErrors) && count($inErrors) > 0)
		{
			$inErrors = array_reverse($inErrors);
			$errorList .= "\n<div class=\"ErrorList\" title=\"Click to close this message.\"><div><ul>";
			
			/* @var $Error Error */
		 	while(($Error = array_pop($inErrors)) !== NULL)
			{
				$errorList .= sprintf("\n<li>%1\$s<br /><br /></li>",
					$Error->getErrorMessage()
				);
				$errorCount++;
			}
			$errorList .= "</ul></div></div>";
		}
		
		$_SESSION['Errors'] = serialize($inErrors);
		
		if($errorCount > 0)
		{ return "<div id=\"ErrorContainer\"></div>".$errorList; }
		else
		{ return NULL; }
	}
	
	/**
	 * Translates the numeric error into a human readable string
	 * @param int $InError
	 * @return string
	 */
	public static function TranslateError($InError)
	{
		global $lang;
		$OutMessage = NULL;
		
		switch($InError)
		{
			case REQUIRED_FIELD_MISSING:
				$OutMessage = $lang->g('ErrorNotAllRequiredData'); break;
			case RIGHTS_ERR_USERNOTALLOWED:
				$OutMessage = $lang->g('ErrorUserActionNotAllowed'); break;
			default:
				$OutMessage = $lang->g('ErrorUnknownError'); break;
		}
		return $OutMessage;
	}
}

class LoginError extends Error
{
	public function LoginError($number = NULL, $message = NULL)
	{ parent::__construct($number, $message); }
	
	/**
	 * Translates the numeric error into a human readable string
	 * @param int $InError
	 * @return string
	 */
	public static function TranslateError($InError)
	{
		global $lang;
		$OutMessage = NULL;
		
		switch($InError)
		{
			case LOGIN_ERR_PASSWORDSNOTIDENTICAL:
				$OutMessage = $lang->g('ErrorLoginErrorPasswordsNotIdentical'); break;
			case LOGIN_ERR_RESETCODENOTFOUND:
				$OutMessage = $lang->g('ErrorLoginErrorHyperlinkInvalid'); break;
			case LOGIN_ERR_USERNAMENOTFOUND:
				$OutMessage = $lang->g('ErrorLoginErrorUsernameNotFound'); break;
			case LOGIN_ERR_USERNAMEANDMAILADDRESNOTFOUND:
				$OutMessage = $lang->g('ErrorLoginErrorUsernameEmailCombo'); break;
			case LOGIN_ERR_PASSWORDINCORRECT:
				$OutMessage = $lang->g('ErrorLoginErrorPasswordIncorect'); break;
			default:
				$OutMessage = $lang->g('ErrorLoginErrorUnknown'); break;
		}
		return $OutMessage;
	}
}

class SQLerror extends Error
{
	public function SQLerror($number = NULL, $message = NULL)
	{ parent::__construct($number, $message); }
	
	/**
	 * Translates the numeric error into a human readable string
	 * @param int $InError
	 * @return string
	 */
	public static function TranslateError($InError)
	{
		$OutMessage = NULL;
		global $lang;
		
		switch($InError)
		{
			case SQL_ERR_NOSUCHTABLE:
				$OutMessage = $lang->g('ErrorSQLErrorTableNotExist'); break;
			default:
				$OutMessage = $lang->g('ErrorSQLErrorUnknown'); break;
		}
		return $OutMessage;
	}
}

class SyntaxError extends Error
{
	public function SyntaxError($number = NULL, $message = NULL)
	{ parent::__construct($number, $message); }
	
	/**
	 * Translates the numeric error into a human readable string
	 * @param int $InError
	 * @return string
	 */
	public static function TranslateError($InError)
	{
		$OutMessage = NULL;
		global $lang;
		
		switch($InError)
		{
			case SYNTAX_ERR_EMAILADDRESS:
				$OutMessage = $lang->g('ErrorSyntaxEmailAddress'); break;
			default:
				$OutMessage = $lang->g('ErrorSyntaxErrorUnknown'); break;
		}
		return $OutMessage;
	}
}

class UploadError extends Error
{
	public function UploadError($number = NULL, $message = NULL)
	{ parent::__construct($number, $message); }
	
	/**
	 * Translates the numeric error ($_FILES['upload']['error']) into a human readable string
	 * @param int $InError
	 * @return string
	 */
	public static function TranslateError($InError)
	{
		$OutMessage = NULL;
		global $lang;
		
		switch($InError)
		{
			case UPLOAD_ERR_OK:
				$OutMessage = $lang->g('ErrorUploadErrorOK'); break;
			case UPLOAD_ERR_INI_SIZE:
				$OutMessage = $lang->g('ErrorUploadErrorMaxFilesizeIni') ; break;
			case UPLOAD_ERR_FORM_SIZE:
				$OutMessage = $lang->g('ErrorUploadErrorMaxFilesize'); break;
			case UPLOAD_ERR_PARTIAL:
				$OutMessage = $lang->g('ErrorUploadErrorPartialUpload'); break;
			case UPLOAD_ERR_NO_FILE:
				$OutMessage = $lang->g('ErrorUploadErrorNoFile'); break;
			case UPLOAD_ERR_NO_TMP_DIR:
				$OutMessage = $lang->g('ErrorUploadErrorMissingTempFolder'); break;
			case UPLOAD_ERR_CANT_WRITE:
				$OutMessage = $lang->g('ErrorUploadErrorWriteToDisk'); break;
			case UPLOAD_ERR_EXTENSION:
				$OutMessage = $lang->g('ErrorUploadErrorPHPExtension'); break;
			default:
				$OutMessage = $lang->g('ErrorUploadErrorUnknown'); break;
		}
		return $OutMessage;
	}
}

class XMLerror extends Error
{
	public function XMLerror($number = NULL, $message = NULL)
	{ parent::__construct($number, $message); }

	/**
	 * Translates the numeric error into a human readable string
	 * @param int $InError
	 * @return string
	 */
	public static function TranslateError($InError)
	{
		$OutMessage = NULL;
		global $lang;

		switch($InError)
		{
			case XML_ERR_XML_VALID:
				$OutMessage = $lang->g('ErrorXMLErrorNotValidXML'); break;
			case XML_ERR_SCHEMA_VALID:
				$OutMessage = $lang->g('ErrorXMLErrorNotValidSchema'); break;
			default:
				$OutMessage = $lang->g('ErrorXMLErrorUnknown'); break;
		}
		return $OutMessage;
	}
}

?>