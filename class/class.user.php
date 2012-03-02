<?php

class User
{
 	private $ID;
 	private $UserName;
 	private $Password;
 	private $Salt;
	private $FirstName;
	private $Insertion;
	private $LastName;
	private $EmailAddress;
	private $Gender = GENDER_UNKNOWN;
	private $BirthDate = -1;
	private $LastActive = -1;
	private $LastLogin = -1;
	private $PreLastLogin = -1;
	private $Rights = 0;
	private $DateDisplayoptions = 0;

	/**
	 * Returns a concatenation of the User's firstname, optional insertion and lastname.
	 * @return string
	 */
	public function GetFullName()
	{
		return sprintf('%1$s%2$s %3$s',
			$this->getFirstName(),
			$this->getInsertion() ? ' '.$this->getInsertion() : null,
			$this->getLastName()
		);
	}

	/**
	 * Returns a concatenation of the Users's title and full name.
	 * @return string
	 */
	public function GetFullNameWithTitle()
	{
		return sprintf('%1$s %2$s',
			$this->FormatTitle($this->getGender()),
			$this->GetFullName());
	}
	
	/**
	 * Formats an appropriate title, according to the specified gender.
	 * @param int $Gender
	 * @return string
	 */
	public function FormatTitle($Gender)
	{
		switch ($Gender){
			default:
			case GENDER_UNKNOWN: return 'Mr./Mrs.'; break;
			case GENDER_FEMALE: return 'Mrs.'; break;
			case GENDER_MALE: return 'Mr.'; break;
		}
	}
	
	
	/**
	 * Instantiates a new User object.
	 * @param int $ID
	 * @param string $UserName
	 * @param string $FirstName
	 * @param string $LastName
	 */
	public function User($ID = null, $UserName = null, $FirstName = null, $LastName = null)
	{
		$this->ID = $ID;
		$this->UserName = $UserName;
		$this->FirstName = $FirstName;
		$this->LastName = $LastName;
	}
	
	/**
	 * Get the User's ID.
	 * @return int
	 */
	public function getID()
	{ return $this->ID; }
	
	/**
	 * @param int $ID
	 */
	public function setID($ID)
	{ $this->ID = $ID; }
	
	/**
	 * Gets the User's username.
	 * @return string 
	 */
	public function getUserName()
	{ return $this->UserName; }
	
	/**
	 * @param string $UserName
	 */
	public function setUserName($UserName)
	{ $this->UserName = $UserName; }
	
	/**
	 * Gets the User's password.
	 * @return string 
	 */
	public function getPassword()
	{ return $this->Password; }
	
	/**
	 * @param string $Password
	 */
	public function setPassword($Password)
	{ $this->Password = $Password; }
	
	/**
	 * Gets the User's salt.
	 * @return string 
	 */
	public function getSalt()
	{ return $this->Salt; }
	
	/**
	 * @param string $Salt
	 */
	public function setSalt($Salt)
	{ $this->Salt = $Salt; }
	
	
	/**
	 * Gets the User's firstname.
	 * @return string 
	 */
	public function getFirstName()
	{ return $this->FirstName; }
	
	/**
	 * @param string $FirstName
	 */
	public function setFirstName($FirstName)
	{ $this->FirstName = $FirstName; }
	
	/**
	 * Gets the User's insertion.
	 * @return string 
	 */
	public function getInsertion()
	{ return $this->Insertion; }
	
	/**
	 * @param string $Insertion
	 */
	public function setInsertion($Insertion)
	{ $this->Insertion = $Insertion; }
	
	/**
	 * Gets the User's lastname.
	 * @return string 
	 */
	public function getLastName()
	{ return $this->LastName; }
	
	/**
	 * @param string $LastName
	 */
	public function setLastName($LastName)
	{ $this->LastName = $LastName; }
	
	/**
	 * Gets the User's e-mailaddress.
	 * @return string 
	 */
	public function getEmailAddress()
	{ return $this->EmailAddress; }
	
	/**
	 * @param string $EmailAddress
	 */
	public function setEmailAddress($EmailAddress)
	{ $this->EmailAddress = $EmailAddress; }
	
	/**
	* Gets the User's date display options
	* @return int
	*/
	public function getDateDisplayOptions()
	{ return $this->DateDisplayoptions; }
	
	/**
	* @param int $DateDisplayoptions
	*/
	public function setDateDisplayOptions($DateDisplayoptions)
	{ $this->DateDisplayoptions = $DateDisplayoptions; }
	
	/**
	* Gets the User's preferred dateformatstring
	* @return string
	*/
	public function getDateFormat()
	{
		global $DateStyleArray;
		return $DateStyleArray[$this->DateDisplayoptions];
	}
	
	/**
	 * Gets the User's gender, represented as a TINYINT, as defined in the main INCLUDE-file.
	 * @return int 
	 */
	public function getGender()
	{ return $this->Gender; }
	
	/**
	 * @param int $Gender
	 */
	public function setGender($Gender)
	{ $this->Gender = $Gender; }
	
	/**
	 * Gets the User's bithdate, represented as a UNIX timstamp.
	 * @return int 
	 */
	public function getBirthDate()
	{ return $this->BirthDate; }
	
	/**
	 * @param int $BirthDate
	 */
	public function setBirthDate($BirthDate)
	{ $this->BirthDate = $BirthDate; }
	
	/**
	 * Gets the User's last active datetime, represented as a UNIX timstamp.
	 * @return int 
	 */
	public function getLastActive()
	{ return $this->LastActive; }
	
	/**
	 * @param int $LastActive
	 */
	public function setLastActive($LastActive)
	{ $this->LastActive = $LastActive; }
	
	/**
	 * Gets the User's last login datetime, represented as a UNIX timstamp.
	 * @return int 
	 */
	public function getLastLogin()
	{ return $this->LastLogin; }
	
	/**
	 * @param int $LastLogin
	 */
	public function setLastLogin($LastLogin)
	{ $this->LastLogin = $LastLogin; }
	
	/**
	 * Gets the User's login datetime before the last login, represented as a UNIX timstamp.
	 * @return int 
	 */
	public function getPreLastLogin()
	{ return $this->PreLastLogin; }
	
	/**
	 * @param int $PreLastLogin
	 */
	public function setPreLastLogin($PreLastLogin)
	{ $this->PreLastLogin = $PreLastLogin; }
	
	/**
	 * Gets the User's rights
	 * @return int
	 */
	public function getRights()
	{ return $this->Rights; }
	
	/**
	 * @param int $Rights
	 */
	public function setRights($Rights)
	{ $this->Rights = $Rights; }





	/**
	 * Gets an array of Users from the database, or NULL on failure. The array can be empty.
	 * @param string $WhereClause
	 * @param string $OrderClause
	 * @param string $LimitClause
	 * @return array(User) | NULL
	 */
	public static function GetUsers($WhereClause = 'mut_deleted = -1', $OrderClause = 'user_lastname ASC, user_firstname ASC', $LimitClause = null)
	{
		global $db;
		
		if($db->Select('User', '*', $WhereClause, $OrderClause, $LimitClause))
		{
			$OutArray = array();
			
			if($db->getResult())
			{
				foreach($db->getResult() as $UserItem)
				{
					$UserObject = new User();
					
					foreach($UserItem as $ColumnKey => $ColumnValue)
					{
						switch($ColumnKey)
						{
							case 'user_id'				: $UserObject->setID($ColumnValue);					break;
							case 'user_username'		: $UserObject->setUserName($ColumnValue);			break;
							case 'user_password'		: $UserObject->setPassword($ColumnValue);			break;
							case 'user_salt'			: $UserObject->setSalt($ColumnValue);				break;
							case 'user_firstname'		: $UserObject->setFirstName($ColumnValue);			break;
							case 'user_insertion'		: $UserObject->setInsertion($ColumnValue);			break;
							case 'user_lastname'		: $UserObject->setLastName($ColumnValue);			break;
							case 'user_email'			: $UserObject->setEmailAddress($ColumnValue);		break;
							case 'user_datedisplayopts'	: $UserObject->setDateDisplayOptions($ColumnValue);	break;
							case 'user_gender'			: $UserObject->setGender($ColumnValue);				break;
							case 'user_birthdate'		: $UserObject->setBirthDate($ColumnValue);			break;
							case 'user_lastactive'		: $UserObject->setLastActive($ColumnValue);			break;
							case 'user_lastlogin'		: $UserObject->setLastLogin($ColumnValue);			break;
							case 'user_prelastlogin'	: $UserObject->setPreLastLogin($ColumnValue);		break;
							case 'user_rights'			: $UserObject->setRights($ColumnValue);				break;
						}
					}
					
					$OutArray[] = $UserObject;
				}
			}
			return $OutArray;
		}
		else
		{ return null; }
	}
	
	/**
	 * Inserts the given user into the database.
	 * @param User $User
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function InsertUser($User, $CurrentUser)
	{
	    global $db;
	    
	    return $db->Insert(
		'User',
		array(
		    mysql_real_escape_string($User->getUserName()),
		    mysql_real_escape_string($User->getPassword()),
		    mysql_real_escape_string($User->getSalt()),
			mysql_real_escape_string($User->getFirstName()),
			mysql_real_escape_string($User->getInsertion()),
		    mysql_real_escape_string($User->getLastName()),
		    mysql_real_escape_string($User->getEmailAddress()),
		    $User->getDateDisplayOptions(),
		    $User->getGender(),
		    $User->getBirthDate(),
		    $CurrentUser->getID(),
		    time()
		),
		'user_username, user_password, user_salt, user_firstname, user_insertion, user_lastname, user_email, user_datedisplayopts, user_gender, user_birthdate, mut_id, mut_date'
	    );
	}
	
	/**
	 * Updates the databaserecord of supplied User.
	 * @param User $User
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function UpdateUser($User, $CurrentUser)
	{
		global $db;
		
		return $db->Update(
			'User',
			array(
				'user_username' => mysql_real_escape_string($User->getUserName()),
				'user_password' => mysql_real_escape_string($User->getPassword()),
				'user_salt' => mysql_real_escape_string($User->getSalt()),
				'user_firstname' => mysql_real_escape_string($User->getFirstName()),
				'user_insertion' => mysql_real_escape_string($User->getInsertion()),
				'user_lastname' => mysql_real_escape_string($User->getLastName()),
				'user_email' => mysql_real_escape_string($User->getEmailAddress()),
				'user_datedisplayopts' => $User->getDateDisplayOptions(),
				'user_gender' => $User->getGender(),
				'user_birthdate' => $User->getBirthDate(),
				'user_lastactive' => $User->getLastActive(),
				'user_lastlogin' => $User->getLastLogin(),
				'user_prelastlogin' => $User->getPreLastLogin(),
				'mut_id' => $CurrentUser->getID(),
				'mut_date' => time()),
			array('user_id', $User->getID())
		);
	}
	
	
	/**
	 * Removes the specified User from the database.
	 * 
	 * @param User $User
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function DeleteUser($User, $CurrentUser)
	{
		global $db;
		
		return $db->Update(
			'User',
			array(
				'mut_id' => $CurrentUser->getID(),
				'mut_deleted' => time()),
			array('user_id', $User->getID())
		);
	}
}

?>