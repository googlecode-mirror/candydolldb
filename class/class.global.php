<?php

class Rights
{
	/**
	 * Returns an array containing all defined right related constants. 
	 * @return array
	 */
	public static function getDefinedRights()
	{
		$c = get_defined_constants();
	
		foreach ($c as $k => $v){
			if(stripos($k, 'RIGHT_') !== 0){
				unset($c[$k]);
			}
		}
	
		uasort($c, function($a, $b){
			return $a == $b ? 0 :
			$a < $b ? -1 : 1;
		});
	
		return $c;
	}
	
	/**
	 * Calculates the sum of all defined right-constants.
	 * @return int
	 */
	public static function getTotalRights()
	{
		return array_reduce(self::getDefinedRights(), function($a, $b){
			$a += $b; return $a;
		});
	}
}

class Authentication
{
	/**
	 * Authenticates this session's User, and returns its object.
	 * @return User
	 */
	public static function Authenticate()
	{
		global $lang;
		
		if(array_key_exists('CurrentUser', $_SESSION))
		{
			/* @var $User User */
			$User = unserialize($_SESSION['CurrentUser']);
			$Users = User::GetUsers(new UserSearchParameters($User->getID(), null, null, $User->getPassword()));
			
			if($Users)
			{
				$User = $Users[0];
				$User->setLastActive(time());
				User::UpdateUser($User, $User);
				
				$lang->setLanguages(array($User->getLanguage()));
				
				return $User;
			}
			else
			{
				header('location:login.php#1');
				exit;
			}
		}
		else
		{
			global $argv, $argc;
			if(isset($argv) && $argc > 0)
			{
				foreach($argv as $arg)
				{
					$kv = explode('=', $arg);
					if(count($kv) > 1)
					{ $_GET[$kv[0]] = $kv[1]; }
					unset($kv); 
				}

				/* Authenticate on the commandline as Default User */
				$Users = User::GetUsers(new UserSearchParameters(CMDLINE_USERID));	
				if($Users)
				{
					$User = $Users[0];
					return $User;
				} 
				else
				{
					return null;
				}
			}
			else
			{
				/* If not on the commandline, the Session expired */
				header('location:login.php?url='.urlencode($_SERVER['REQUEST_URI']));
				exit;
			}
		}
	}
}

class BusyIndicator
{
	private static $chars = '-\|/';
	private $stringPrefix;
	private $currentCharIndex;
	private $maxValue;
	private $currentValue;

	/**
	 * Creates a new busy-indicator from the supplied arguments.
	 * @param int $maxValue
	 * @param int $startValue
	 * @param string $prefix
	 */
	public function BusyIndicator($maxValue = 100, $startValue = 0, $prefix = '')
	{
		$this->currentCharIndex = 0;
		$this->maxValue = $maxValue;
		$this->currentValue = $startValue;
		$this->stringPrefix = $prefix;
	}
	
	/**
	 * Calculates the current value of the busy-indicator.
	 * @return float
	 */
	private function CalcValue()
	{
		return (
			($this->currentValue ? $this->currentValue : 0) / 
			($this->maxValue ? $this->maxValue : 100) 
		);
	}
	
	/**
	 * Prints a number of BACKSPACE-characters equal in length to the input string.
	 * @param string $inString
	 */
	private static function SweepItClean($inString)
	{
		for($i = 0; $i < strlen($inString); $i++)
		{ printf("\x08"); }
	}
	
	/**
	 * Advances the busy-indicator by the given amount and draws all output to screen.
	 * @param int $step
	 */
	public function Next($step = 1)
	{
		$toWrite = sprintf('%1$s %2$s %3$6.2f%%',
			$this->stringPrefix,
			substr(self::$chars, $this->currentCharIndex, 1),
			$this->CalcValue() * 100
		);

		echo $toWrite;
		echo self::SweepItClean($toWrite);
		
		$this->currentCharIndex++;
		
		if($this->currentCharIndex > strlen(self::$chars) - 1)
		{ $this->currentCharIndex = 0; }
	
		$this->currentValue += $step;
	}
	
	/**
	 * Draws the word 'Finished' and a trailing newline to the commandline.
	 */
	public function Finish()
	{
		global $lang;
		
		printf(
			"%1\$s %2\$s\n",
			$this->stringPrefix,
			$lang->g('CLIFinished')
		);
	}
}

class Utils
{
	/**
	 * Returns a human readable string of a filesize, e.g. 2,43 MiB.
	 * @param int $SizeInBytes
	 * @return string
	 */
	public static function ReadableFilesize($SizeInBytes)
	{
		$OutString = sprintf('%1$d B', $SizeInBytes);
		
		if($SizeInBytes >= 1024)
		{ $OutString = sprintf('%1$.0f KiB', $SizeInBytes / 1024); }
		
		if($SizeInBytes >= pow(1024, 2))
		{ $OutString = sprintf('%1$.2f MiB', $SizeInBytes / pow(1024, 2)); }
		
		if($SizeInBytes >= pow(1024, 3))
		{ $OutString = sprintf('%1$.2f GiB', $SizeInBytes / pow(1024, 3)); }
		
		if($SizeInBytes >= pow(1024, 4))
		{ $OutString = sprintf('%1$.2f TiB', $SizeInBytes / pow(1024, 4)); }
		
		if($SizeInBytes >= pow(1024, 5))
		{ $OutString = sprintf('%1$.2f PiB', $SizeInBytes / pow(1024, 5)); }
		
		if($SizeInBytes >= pow(1024, 6))
		{ $OutString = sprintf('%1$.2f EiB', $SizeInBytes / pow(1024, 6)); }
		
		if($SizeInBytes >= pow(1024, 7))
		{ $OutString = sprintf('%1$.2f ZiB', $SizeInBytes / pow(1024, 7)); }
		
		if($SizeInBytes >= pow(1024, 8))
		{ $OutString = sprintf('%1$.2f YiB', $SizeInBytes / pow(1024, 8)); }
		
		return $OutString;
	}
	
	/**
	 * Searches the GET-array for a key named $name and returns its absolute integer value, or NULL on failure.
	 * @param string $name
	 * @return int
	 */
	public static function SafeIntFromQS($name)
	{
		if(array_key_exists($name, $_GET) && isset($_GET[$name]) && is_numeric($_GET[$name]))
		{ return abs((int)$_GET[$name]); }
		
		return null;
	}

	/**
	* Searches the GET-array for a key named $name and returns its value (TRUE or FALSE), or FALSE on failure.
	* @param string $name
	* @return bool
	*/
	public static function SafeBoolFromQS($name)
	{
		return (
			array_key_exists($name, $_GET) &&
			isset($_GET[$name]) &&
			($_GET[$name] == 'true' || $_GET[$name] == '1')
		);
	}
	
	/**
	 * Returns an array of integers, safe to be processed in, for example, an SQL-in-query
	 * @param array $inArray
	 * @return array
	 */
	public static function SafeInts($inArray)
	{
		$outArray = array();
	
		if(is_array($inArray)){
			foreach ($inArray as $value) {
				$outArray[] = abs(intval($value));
			}
		}
	
		return array_unique($outArray);
	}
	
	/**
	 * Validates the given string to be a valid emailaddress.
	 * @param string $InAddress
	 * @return bool
	 */
	public static function ValidateEmail($InAddress)
	{
		if(strlen($InAddress) > 253) { return false; }
		$EmailPattern = "/^[a-z0-9]   ( [-a-z0-9_] | \.(?!\.) )*    [a-z0-9]   @   [a-z0-9]{2,}  ( [-a-z0-9_] | \.(?!\.)  )*   \.[a-z]{2,}  $ /ix";
		return preg_match($EmailPattern, $InAddress) > 0;
	}
	
	
	/**
	 * @param int $TimeStamp
	 * @return float
	 */
	public static function CalculateAge($TimeStamp)
	{
		$diff = time() - (int)$TimeStamp;
		$age = (float)($diff / 60 / 60 / 24 / 365.25);
		return $age;
	}

	/**
	 * @param int $GarbageLength
	 * @return string
	 */
	public static function GenerateGarbage($GarbageLength)
	{
		$Garbage = '';
		$CharsToChooseFrom = 'abcdefghijklmnopqrstuvwxyz';
		$CharsToChooseFrom .= strtoupper($CharsToChooseFrom);
		$CharsToChooseFrom .= '0123456789';
		$CharsToChooseFromLength = strlen($CharsToChooseFrom);

		for($i = 0; $i < $GarbageLength; $i++){
			$Garbage .= substr($CharsToChooseFrom, rand(0, $CharsToChooseFromLength), 1);
		}
		return $Garbage;
	}
	
	/**
	 * @return string
	 */
	public static function UUID()
	{
		if (function_exists('com_create_guid') === true)
		{ return strtolower(trim(com_create_guid(), '{}')); }

		return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			mt_rand(0, 65535),
			mt_rand(0, 65535),
			mt_rand(0, 65535),
			mt_rand(16384, 20479),
			mt_rand(32768, 49151),
			mt_rand(0, 65535),
			mt_rand(0, 65535),
			mt_rand(0, 65535));
	}
	
	/**
	 * Returns a hashed and salted version of the input string.
	 * @param string $PlainTextString
	 * @param string $Salt
	 * @return string
	 */
	public static function HashString($PlainTextString, $Salt)
	{
		$OutHash = $PlainTextString;
		for ($i = 0; $i < 20000; $i++)
		{
			if($i % 2 == 0) { $OutHash = hash('sha512', $OutHash.$Salt, false); }
			else {            $OutHash = hash('sha512', $Salt.$OutHash, false); }
		}
		return $OutHash;
	}
}

?>