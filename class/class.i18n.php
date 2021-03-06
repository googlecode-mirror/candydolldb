<?php

class Labels
{
	
}

class i18n
{
	/**
	 * Array containing ISO-codes of supported languages
	 * @var array(string)
	 */
	public static $SupportedLanguages = array('en', 'nl');

	/**
	 * Checks if the supplied language is supported
	 * @param string $lang
	 */
	private static function isSupported($lang)
	{
		$lang = strtolower((string)$lang);
		return in_array($lang, self::$SupportedLanguages);
	}
	
	/**
	 * The preferred languages of this i18n instance 
	 * @var array(string)
	 */
	private $preferredLanguages;
	
	/**
	 * Returns the preferred languages of this i18n instance
	 * @return array(string)
	 */
	public function getCurrent()
	{
		return $this->preferredLanguages;
	}
	
	/**
	 * Sets the preferred languages of this i18n instance
	 * @param array(string) $langs
	 */
	public function setLanguages($langs)
	{
		$this->preferredLanguages = $langs;
	}

	/**
	 * Constructs a i18n object, defaulting the CurrentLanguage to the first supported language (currently English) 
	 */
	public function __construct()
	{
		$this->preferredLanguages = self::$SupportedLanguages; 
	}
	
	public function g($key)
	{
		return $this->getString($key);
	}
	
	public function getString($key)
	{
		foreach ($this->preferredLanguages as $lang)
		{
			if(self::isSupported($lang))
			{
				switch ($lang){
					
					case 'en':
						if(LabelsEN::LabelExists($key))
						{ return utf8_decode(LabelsEN::$Labels[$key]); }
						break;
						
					case 'nl':
						if(LabelsNL::LabelExists($key))
						{ return utf8_decode(LabelsNL::$Labels[$key]); }
						break;
							
					case 'de':
						if(LabelsDE::LabelExists($key))
						{ return utf8_decode(LabelsDE::$Labels[$key]); }
						break;
				}
			}
		}
		return '?';
	}
}

?>