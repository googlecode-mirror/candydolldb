<?php

class Authentication
{
	/**
	 * Authenticates this session's User, and returns its object.
	 * @return User
	 */
	public static function Authenticate()
	{
		if(array_key_exists('CurrentUser', $_SESSION))
		{
			/* @var $User User */
			$User = unserialize($_SESSION['CurrentUser']);
			
			$WhereClause = sprintf("user_id = %1\$d AND user_password = '%2\$s' AND mut_deleted = -1",
				$User->getID(),
				mysql_real_escape_string($User->getPassword())
			);
			
			$Users = User::GetUsers($WhereClause);
			
			if($Users)
			{
				$User = $Users[0];
				$User->setLastActive(time());
				User::UpdateUser($User, $User);
				
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
				$Users = User::GetUsers(sprintf('user_id = %1$d', CMDLINE_USERID));	
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
		if($this->currentValue + $step >= $this->maxValue)
		{
			$toWrite = sprintf("%1\$s Finished.\n", $this->stringPrefix);
			echo $toWrite;
		}
		else
		{
			$toWrite = sprintf('%1$s %2$s %3$6.2f%%',
				$this->stringPrefix,
				substr(BusyIndicator::$chars, $this->currentCharIndex, 1),
				$this->CalcValue() * 100
			);

			echo $toWrite;
			echo BusyIndicator::SweepItClean($toWrite);

			$this->currentValue = $this->currentValue + $step;
			$this->currentCharIndex++;

			if($this->currentCharIndex > strlen(BusyIndicator::$chars) - 1)
			{ $this->currentCharIndex = 0; }
		}
	}
}

class Utils
{
	/**
	 * Returns a human readable string of a filesize, e.g. 2,43 MB.
	 * @param int $SizeInBytes
	 * @return string
	 */
	public static function ReadableFilesize($SizeInBytes)
	{
		$OutString = sprintf('%1$d B', $SizeInBytes);
		
		if($SizeInBytes >= 1024)
		{ $OutString = sprintf('%1$.0f KB', $SizeInBytes / 1024);  }
		
		if($SizeInBytes >= 1024 * 1024)
		{ $OutString = sprintf('%1$.2f MB', $SizeInBytes / 1024 / 1024);  }
		
		if($SizeInBytes >= 1024 * 1024 * 1024)
		{ $OutString = sprintf('%1$.2f GB', $SizeInBytes / 1024 / 1024 / 1024);  }
		
		if($SizeInBytes >= 1024 * 1024 * 1024 * 1024)
		{ $OutString = sprintf('%1$.2f TB', $SizeInBytes / 1024 / 1024 / 1024 / 1024);  }
		
		return $OutString;
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
				$outArray[] = intval($value);
			}
		}
	
		return array_unique($outArray);
	}
	
	/**
	 * Validates the given string to be a valid emailaddress.
	 * @param string $InAddress
	 * @return boolean
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
	public static function GUID()
	{
		if (function_exists('com_create_guid') === true)
		{ return trim(com_create_guid(), '{}'); }

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
