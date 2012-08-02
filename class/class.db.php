<?php

class DB
{
	private $DBHost = '';
	private $DBUsername = '';
	private $DBPassword = '';
	private $Connection;
	private $DatabaseName = '';
	private $ConnectionEstablished = false;
	private $Result = array();
	private $NumResults = 0;
	private $FieldInfo = null;

	/**
	 * Instantiates a MySQL databaseconnection.
	 * @param string $DBHost, the host to connect to
	 * @param string $DBUsername, the username to use
	 * @param string $DBPassword, the password to use
	 * @example $db = new DB('localhost', 'myuser', 'myp@ssw0rd');
	 */
	public function __construct($DBHost, $DBUsername, $DBPassword)
	{
		$this->DBHost = $DBHost;
		$this->DBUsername = $DBUsername;
		$this->DBPassword = $DBPassword;
	}

	/**
	 * Connects to the database.
	 * @return bool
	 */
	public function Connect()
	{
		if(!$this->ConnectionEstablished)
		{
			$this->Connection = @mysql_pconnect($this->DBHost, $this->DBUsername, $this->DBPassword);

			if($this->Connection)
			{
				@mysql_query('SET GLOBAL sql_mode = \'STRICT_ALL_TABLES\';');
				$this->ConnectionEstablished = true;
				return $this->ConnectionEstablished;
			}
			else
			{ return false; }
		}
		else
		{ return true; }
	}

	public function getResult()
	{ return $this->Result; }

	public function getFieldInfo()
	{ return $this->FieldInfo; }

	public function getConnection()
	{ return $this->Connection; }

	public function getConnectionEstablished()
	{ return $this->ConnectionEstablished; }

	public function getDatabaseName()
	{ return $this->DatabaseName; }
	
	/**
	 * @param string $DatabaseName
	 * @return bool
	 */
	public function setDatabaseName($DatabaseName)
	{
		$this->DatabaseName = $DatabaseName;

		if($this->ConnectionEstablished)
		{ return @mysql_select_db($this->DatabaseName, $this->Connection); }
		else
		{ return false; }
	}

	/**
	 * Executes the given SQL statement(s).
	 * @param string $SQL
	 * @return boolean
	 */
	public function ExecuteQueries($SQL)
	{
		global $SplitRegex;
		$queries = preg_split($SplitRegex, $SQL);
		
		if($this->ConnectionEstablished)
		{
			$OutBool = true;
			foreach($queries as $q)
			{	
				if(strlen(trim($q)) == 0) { continue; }
				
				$OutBool = @mysql_query($q);
				
				if($OutBool === false) { break; }
			}
			return $OutBool;			
		}
		else
		{ return false; }
	}
}

?>