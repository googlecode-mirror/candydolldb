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
			{ header('location:login.php'); }
		}
		else
		{
			/* Authenticate on the commandline as FWieP */
			global $argv, $argc;
			if($argv && $argc > 0)
			{
				foreach($argv as $arg)
				{
					$kv = explode('=', $arg);
					if(count($kv) > 1)
					{ $_GET[$kv[0]] = $kv[1]; }
					unset($kv); 
				}
				
				$Users = User::GetUsers(sprintf('user_id = %1$d', CMDLINE_USERID));	
				if($Users)
				{
					$User = $Users[0];
					return $User;
				} 
				else
				{ return null; }
			}
			else
			{ header('location:login.php'); }
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
		return sha1($PlainTextString.$Salt);
	}
}

?>