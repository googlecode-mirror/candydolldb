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
	
	private $DateDisplayoptions = 0;
	private $Imageview = 'detail';
	private $Language = 'en';
	private $Rights = array();
	
	private $LastActive = -1;
	private $LastLogin = -1;
	private $PreLastLogin = -1;

	/**
	 * @param int $user_id
	 * @param string $user_username
	 * @param string $user_password
	 * @param string $user_salt
	 * @param string $user_firstname
	 * @param string $user_insertion
	 * @param string $user_lastname
	 * @param string $user_email
	 * @param int $user_gender
	 * @param int $user_birthdate
	 * @param int $user_datedisplayopts
	 * @param string $user_imageview
	 * @param string $user_language
	 * @param array(int) $user_rights
	 * @param int $user_lastactive
	 * @param int $user_lastlogin
	 * @param int $user_prelastlogin
	 */
	public function __construct(
		$user_id = NULL, $user_username = NULL, $user_password = NULL, $user_salt = NULL,
		$user_firstname = NULL, $user_insertion = NULL, $user_lastname = NULL, $user_email = NULL,
		$user_gender = GENDER_UNKNOWN, $user_birthdate = -1,
		$user_datedisplayopts = 0, $user_imageview = 'detail', $user_language = 'en', $user_rights = array(),
		$user_lastactive = -1, $user_lastlogin = -1, $user_prelastlogin = -1)
	{
		$this->ID = $user_id;
		$this->UserName = $user_username;
		$this->Password = $user_password;
		$this->Salt = $user_salt;
	
		$this->FirstName = $user_firstname;
		$this->Insertion = $user_insertion;
		$this->LastName = $user_lastname;
		$this->EmailAddress = $user_email;
	
		$this->Gender = $user_gender;
		$this->BirthDate = $user_birthdate;
	
		$this->DateDisplayoptions = $user_datedisplayopts;
		$this->Imageview = $user_imageview;
		$this->Language = $user_language;
		$this->Rights = $user_rights;
	
		$this->LastActive = $user_lastactive;
		$this->LastLogin = $user_lastlogin;
		$this->PreLastLogin = $user_prelastlogin;
	}
	
	/**
	 * Returns a concatenation of the User's firstname, optional insertion and lastname.
	 * @return string
	 */
	public function GetFullName()
	{
		return sprintf('%1$s%2$s %3$s',
			$this->getFirstName(),
			$this->getInsertion() ? ' '.$this->getInsertion() : NULL,
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
		global $lang;
		
		switch ($Gender){
			default:
			case GENDER_UNKNOWN: return $lang->g('LabelTitleMrMrs'); break;
			case GENDER_FEMALE: return $lang->g('LabelTitleMrs'); break;
			case GENDER_MALE: return $lang->g('LabelTitleMr'); break;
		}
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
	* Gets the User's image display options
	* @return string
	*/
	public function getImageview()
	{ return $this->Imageview; }

	/**
	* @param string $Imageview
	*/
	public function setImageview($Imageview)
	{ $this->Imageview = $Imageview; }

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
	 * @return array(int)
	 */
	public function getRights()
	{ return $this->Rights; }
	
	/**
	 * @param array(int) $Rights
	 */
	public function setRights($Rights)
	{ $this->Rights = $Rights; }

	/**
	 * Gets the User's language
	 * @return string
	 */
	public function getLanguage()
	{ return $this->Language; }
	
	/**
	 * @param string $Language
	 */
	public function setLanguage($Language)
	{ $this->Language = $Language; }

	/**
	 * Checks this user's rights for the given permission
	 * @param int $permission
	 * @return bool
	 */
	public function hasPermission($permission)
	{ return self::CheckPermission($this->Rights, $permission); }
	
	/**
	 * Gets an array of Users from the database, or NULL on failure.
	 * @param UserSearchParameters $SearchParameters
	 * @param string $OrderClause
	 * @param string $LimitClause
	 * @return array(User) | NULL
	 */
	public static function GetUsers($SearchParameters = NULL, $OrderClause = 'user_lastname ASC, user_firstname ASC', $LimitClause = NULL)
	{
		global $dbi;
		$SearchParameters = $SearchParameters ? $SearchParameters : new UserSearchParameters();
		$OrderClause = empty($OrderClause) ? 'user_lastname ASC, user_firstname ASC' : $OrderClause;
		
		$q = sprintf("
			SELECT
				`user_id`, `user_username`, `user_password`, `user_salt`,
				`user_firstname`, `user_insertion`, `user_lastname`, `user_email`,
				`user_gender`, `user_birthdate`,
				`user_datedisplayopts`, `user_imageview`, `user_language`, `user_rights`,
				`user_lastactive`, `user_lastlogin`, `user_prelastlogin`
			FROM
				`User`
			WHERE
				mut_deleted = -1
				%1\$s
			ORDER BY
				%2\$s
			%3\$s",
			$SearchParameters->getWhere(),
			$OrderClause,
			$LimitClause ? ' LIMIT '.$LimitClause : NULL
		);
		
		if(!($stmt = $dbi->prepare($q)))
		{
			$e = new SQLerror($dbi->errno, $dbi->error);
			Error::AddError($e);
			return NULL;
		}
		
		DBi::BindParamsToSelect($SearchParameters, $stmt);
		
		if($stmt->execute())
		{
			$OutArray = array();
			$stmt->bind_result(
				$user_id, $user_username, $user_password, $user_salt,
				$user_firstname, $user_insertion, $user_lastname, $user_email,
				$user_gender, $user_birthdate,
				$user_datedisplayopts, $user_imageview, $user_language, $user_rights,
				$user_lastactive, $user_lastlogin, $user_prelastlogin);
			
			while($stmt->fetch())
			{
				// Unserialize the non-NULL value in the db, catch dev-time INT
				$user_rights = $user_rights && !is_numeric($user_rights) ? unserialize($user_rights) : array();
				
				$o = new self(
					$user_id, $user_username, $user_password, $user_salt,
					$user_firstname, $user_insertion, $user_lastname, $user_email,
					$user_gender, $user_birthdate,
					$user_datedisplayopts, $user_imageview, $user_language, $user_rights,
					$user_lastactive, $user_lastlogin, $user_prelastlogin);
				
				$OutArray[] = $o;
			}
			
			$stmt->close();
			return $OutArray;
		}
		else
		{
			$e = new SQLerror($dbi->errno, $dbi->error);
			Error::AddError($e);
			return NULL;
		}
	}
	
	/**
	 * Inserts the given user into the database.
	 * @param User $User
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function Insert($User, $CurrentUser)
	{
	    return self::InsertMulti(array($User), $CurrentUser);
	}
	
	/**
	 * Inserts the given users into the database.
	 * @param array(User) $Users
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function InsertMulti($Users, $CurrentUser)
	{
		global $dbi;
		$outBool = TRUE;

		$mut_id = $CurrentUser->getID();
		$mut_date = time();
	
		if(!is_array($Users))
		{ return FALSE; }
	
		$q = sprintf("
			INSERT INTO	`User` (
				`user_username`,
				`user_password`,
				`user_salt`,
				`user_firstname`,
				`user_insertion`,
				`user_lastname`,
				`user_email`,
				`user_datedisplayopts`,
				`user_rights`,
				`user_imageview`,
				`user_language`,
				`user_gender`,
				`user_birthdate`,
				`mut_id`,
				`mut_date`
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		");
	
		if(!($stmt = $dbi->prepare($q)))
		{
			$e = new SQLerror($dbi->errno, $dbi->error);
			Error::AddError($e);
			return FALSE;
		}
	
		$stmt->bind_param('sssssssisssiiii',
			$user_username,
			$user_password,
			$user_salt,
			$user_firstname,
			$user_insertion,
			$user_lastname,
			$user_email,
			$user_datedisplayopts,
			$user_rights,
			$user_imageview,
			$user_language,
			$user_gender,
			$user_birthdate,
			$mut_id,
			$mut_date
		);
	
		foreach($Users as $User)
		{
			$user_username = $User->getUserName();
			$user_password = $User->getPassword();
			$user_salt = $User->getSalt();
			$user_firstname = $User->getFirstName();
			$user_insertion = $User->getInsertion();
			$user_lastname = $User->getLastName();
			$user_email = $User->getEmailAddress();
			$user_datedisplayopts = $User->getDateDisplayOptions();
			$user_rights = serialize($User->getRights());
			$user_imageview = $User->getImageview();
			$user_language = $User->getLanguage();
			$user_gender = $User->getGender();
			$user_birthdate = $User->getBirthDate();
			
			$outBool = $stmt->execute();
			if($outBool)
			{
				$User->setID($dbi->insert_id);
			}
			else
			{
				$e = new SQLerror($dbi->errno, $dbi->error);
				Error::AddError($e);
			}
		}
	
		$stmt->close();
		return $outBool;
	}
	
	/**
	 * Updates the databaserecord of supplied User.
	 * @param User $User
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function Update($User, $CurrentUser)
	{
		return self::UpdateMulti(array($User), $CurrentUser);
	}
	
	/**
	 * Updates the databaserecords of supplied Users.
	 * @param array(User) $Users
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function UpdateMulti($Users, $CurrentUser)
	{
		global $dbi;
		$outBool = TRUE;

		$mut_id = $CurrentUser->getID();
		$mut_date = time();
	
		if(!is_array($Users))
		{ return FALSE; }
	
		$q = sprintf("
			UPDATE `User` SET
				`user_username` = ?,
				`user_password` = ?,
				`user_salt` = ?,
				`user_firstname` = ?,
				`user_insertion` = ?,
				`user_lastname` = ?,
				`user_email` = ?,
				`user_datedisplayopts` = ?,
				`user_rights` = ?,
				`user_imageview` = ?,
				`user_language` = ?,
				`user_gender` = ?,
				`user_birthdate` = ?,
				`user_lastactive` = ?,
				`user_lastlogin` = ?,
				`user_prelastlogin` = ?,
				`mut_id` = ?,
				`mut_date` = ?
			WHERE
				`user_id` = ?
		");
	
		if(!($stmt = $dbi->prepare($q)))
		{
			$e = new SQLerror($dbi->errno, $dbi->error);
			Error::AddError($e);
			return FALSE;
		}
	
		$stmt->bind_param('sssssssisssiiiiiiii',
			$user_username,
			$user_password,
			$user_salt,
			$user_firstname,
			$user_insertion,
			$user_lastname,
			$user_email,
			$user_datedisplayopts,
			$user_rights,
			$user_imageview,
			$user_language,
			$user_gender,
			$user_birthdate,
			$user_lastactive,
			$user_lastlogin,
			$user_prelastlogin,
			$mut_id,
			$mut_date,
			$id
		);
	
		/* @var $User User */
		foreach($Users as $User)
		{
			$user_username = $User->getUserName();
			$user_password = $User->getPassword();
			$user_salt = $User->getSalt();
			$user_firstname = $User->getFirstName();
			$user_insertion = $User->getInsertion();
			$user_lastname = $User->getLastName();
			$user_email = $User->getEmailAddress();
			$user_datedisplayopts = $User->getDateDisplayOptions();
			$user_rights = serialize($User->getRights());
			$user_imageview = $User->getImageview();
			$user_language = $User->getLanguage();
			$user_gender = $User->getGender();
			$user_birthdate = $User->getBirthDate();
			$user_lastactive = $User->getLastActive();
			$user_lastlogin = $User->getLastLogin();
			$user_prelastlogin = $User->getPreLastLogin();
			$id = $User->getID();
			
			$outBool = $stmt->execute();
			if(!$outBool)
			{
				$e = new SQLerror($dbi->errno, $dbi->error);
				Error::AddError($e);
			}
		}
	
		$stmt->close();
		return $outBool;
	}
	
	/**
	 * Removes the specified User from the database.
	 * @param User $User
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function Delete($User, $CurrentUser)
	{
		return self::DeleteMulti(array($User), $CurrentUser);
	}
	
	/**
	 * Removes the specified Users from the database.
	 * @param array(User) $Users
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function DeleteMulti($Users, $CurrentUser)
	{
		global $dbi;
		$outBool = TRUE;

		$id = NULL;
		$mut_id = $CurrentUser->getID();
		$mut_deleted = time();
	
		if(!is_array($Users))
		{ return FALSE; }
	
		$q = sprintf("
			UPDATE `User` SET
				`mut_id` = ?,
				`mut_deleted` = ?
			WHERE
				`user_id` = ?
		");
	
		if(!($stmt = $dbi->prepare($q)))
		{
			$e = new SQLerror($dbi->errno, $dbi->error);
			Error::AddError($e);
			return FALSE;
		}
	
		$stmt->bind_param('iii',
			$mut_id,
			$mut_deleted,
			$id
		);
	
		foreach($Users as $User)
		{
			$id = $User->getID();
			$outBool = $stmt->execute();

			if(!$outBool)
			{
				$e = new SQLerror($dbi->errno, $dbi->error);
				Error::AddError($e);
			}
		}
	
		$stmt->close();
		return $outBool;
	}

	/**
	 * Checks permission against given collection of rights.
	 * @param int $Rights
	 * @param int $Permission
	 * @return bool
	 */
 	public static function CheckPermission($Rights, $Permission)
 	{
 		return in_array($Permission, $Rights);
 	}
}

class UserSearchParameters extends SearchParameters
{
	private $paramtypes = '';
	private $values = array();
	private $where = '';

	public function __construct($SingleID = FALSE, $MultipleIDs = FALSE, $UserName = FALSE, $Password = FALSE, $Email = FALSE)
	{
		parent::__construct();

		if($SingleID !== FALSE)
		{
			$this->paramtypes .= "i";
			$this->values[] = $SingleID;
			$this->where .= " AND user_id = ?";
		}

		if(is_array($MultipleIDs) && count($MultipleIDs) > 0)
		{
			$this->paramtypes .= str_repeat('i', count($MultipleIDs));
			$this->values = array_merge($this->values, $MultipleIDs);
			$this->where .= sprintf(" AND user_id IN ( %1s ) ",
				implode(', ', array_fill(0, count($MultipleIDs), '?'))
			);
		}

		if($UserName !== FALSE)
		{
			$this->paramtypes .= 's';
			$this->values[] = $UserName;
			$this->where .= " AND user_username = ?";
		}

		if($Password !== FALSE)
		{
			$this->paramtypes .= 's';
			$this->values[] = $Password;
			$this->where .= " AND user_password = ?";
		}

		if($Email !== FALSE)
		{
			$this->paramtypes .= 's';
			$this->values[] = $Email;
			$this->where .= " AND user_email = ?";
		}
	}

	public function getWhere()
	{ return $this->where; }

	public function getValues()
	{ return $this->values; }

	public function getParamTypes()
	{ return $this->paramtypes; }
}

?>