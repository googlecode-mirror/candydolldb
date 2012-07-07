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
	public function DB($DBHost, $DBUsername, $DBPassword)
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


	/**
	 * Disconnects from the current database, if any.
	 * @return bool, TRUE on success, FALSE on failure
	 */
	public function Disconnect()
	{
		if($this->ConnectionEstablished)
		{
			if(@mysql_close($this->Connection))
			{
				$this->ConnectionEstablished = false;
				return true;
			}
			else
			{ return false; }
		}
		else
		{ return false; }
	}

	/**
	 * Checks whether a given table exists in the current database.
	 * @return bool, TRUE on success, FALSE on failure
	 */
	private function TableExists($Table)
	{
		$TableInDb = @mysql_query('SHOW TABLES FROM '.$this->DatabaseName.' LIKE "'.$Table.'"');
		
		if($TableInDb)
		{ return (mysql_num_rows($TableInDb)==1); }
		else
		{ return false; }
	}

	/**
	 * Returns the latest identity, or FALSE on failure
	 * @return int|bool
	 */
	public function GetLatestID()
	{
		$query = @mysql_query('SELECT LAST_INSERT_ID() AS `ID`;');
			
		if($query)
		{
			$row = mysql_fetch_array($query);
			return (int)$row['ID'];
		}
		else 
		{ return false; }
	}
	
	/**
	 * Executes the given SELECT statement and places the resultset into $db->result()
	 * @param string $q
	 * @return bool
	 */
	public function ExecuteSelect($q)
	{
		$query = @mysql_query($q);
			
		if($query)
		{
			$this->Result = array();
			$this->FieldInfo = array();
			$numCols = mysql_num_fields($query);
		
			if($numCols > 0)
			{
				for($k = 0; $k < $numCols; $k++)
				{ $this->FieldInfo[] = mysql_fetch_field($query, $k); }
			}
				
			$this->NumResults = mysql_num_rows($query);
			for($i = 0; $i < $this->NumResults; $i++)
			{
				$r = mysql_fetch_array($query);
				$key = array_keys($r);
		
				for($x = 0; $x < count($key); $x++)
				{
					if(!is_int($key[$x]))
					{
						if($this->NumResults > 0)
						{
							switch(DB::FindType($this->FieldInfo, $key[$x]))
							{
								case 'int':
									$this->Result[$i][$key[$x]] = (strlen($r[$key[$x]]) == 0 ? null : (int)$r[$key[$x]]);
									break;
										
								case 'string':
								default:
									$this->Result[$i][$key[$x]] = (strlen($r[$key[$x]]) == 0 ? null : (string)$r[$key[$x]]);
									break;
							}
						}
						else
						{$this->Result = null; }
					}
				}
			}
			return true;
		}
		else
		{
			$SQLError = new SQLerror();
			$SQLError->setErrorNumber(mysql_errno());
			$SQLError->setErrorMessage(mysql_error());
			Error::AddError($SQLError);
			return false;
		}
	}

	/**
	 * @param string $Table
	 * @param string $Columns
	 * @param string $Where
	 * @param string $Order
	 * @param string $Limit
	 * @return bool, TRUE on success, FALSE on failure
	 */
	public function Select($Table, $Columns = '*', $Where = null, $Order = null, $Limit = null)
	{
		// Thanks to http://net.tutsplus.com/
		$q = 'SELECT '.$Columns.' FROM '.$Table;

		if($Where != null)
		{ $q .= ' WHERE '.$Where; }

		if($Order != null)
		{ $q .= ' ORDER BY '.$Order; }
		
		if($Limit != null)
		{ $q .= ' LIMIT '.$Limit; }

		if($this->TableExists($Table))
		{
			return $this->ExecuteSelect($q);
		}
		else
		{
			$SQLError = new SQLerror();
			$SQLError->setErrorNumber(SQL_ERR_NOSUCHTABLE);
			$SQLError->setErrorMessage(SQLerror::TranslateSQLError(SQL_ERR_NOSUCHTABLE));
			Error::AddError($SQLError);
			return false;
		}
	}

	/**
	 * @param string $Table
	 * @param array() $Values
	 * @param string $Columns, comma separated column names
	 * @param bool $AddIgnore, add the IGNORE keyword
	 * @return bool, TRUE on success, FALSE on failure
	 */
	public function Insert($Table, $Values, $Columns = null, $AddIgnore = false)
	{
		//Thanks to http://net.tutsplus.com/
		if($this->TableExists($Table))
		{
			$Insert = 'INSERT ';
			
			if($AddIgnore)
			{ $Insert .= ' IGNORE'; }
			
			$Insert .= ' INTO `'.$Table.'`';

			if($Columns != null)
			{ $Insert .= ' ('.$Columns.')'; }

			for($i = 0; $i < count($Values); $i++)
			{
				if(is_null($Values[$i]) || strlen($Values[$i]) == 0)
				{ $Values[$i] = 'NULL'; }
				else if(is_string($Values[$i]))
				{ $Values[$i] = '"'.$Values[$i].'"'; }
			}
			
			$Values = implode(', ',$Values);
			$Insert .= ' VALUES ('.$Values.')';
			
			$ins = @mysql_query($Insert);
			
			if($ins)
			{ return true; }
			else
			{
				$SQLError = new SQLerror();
				$SQLError->setErrorNumber(mysql_errno());
				$SQLError->setErrorMessage(mysql_error());
				Error::AddError($SQLError);
				return false;
			}
		}
		else
		{
			$SQLError = new SQLerror();
			$SQLError->setErrorNumber(SQL_ERR_NOSUCHTABLE);
			$SQLError->setErrorMessage(SQLerror::TranslateSQLError(SQL_ERR_NOSUCHTABLE));
			Error::AddError($SQLError);
			return false;
		}
	}

	
	/**
	 * @param string $Table
	 * @param string $Where
	 * @return bool, TRUE on success, FALSE on failure
	 */
	public function Delete($Table, $Where)
	{
		if($this->TableExists($Table))
		{
			$Delete = 'DELETE FROM `'.$Table.'` WHERE '.$Where;
			$del = @mysql_query($Delete);

			if($del)
			{ return true; }
			else
			{
				$SQLError = new SQLerror();
				$SQLError->setErrorNumber(mysql_errno());
				$SQLError->setErrorMessage(mysql_error());
				Error::AddError($SQLError);
				return false;
			}
		}
		else
		{
			$SQLError = new SQLerror();
			$SQLError->setErrorNumber(SQL_ERR_NOSUCHTABLE);
			$SQLError->setErrorMessage(SQLerror::TranslateSQLError(SQL_ERR_NOSUCHTABLE));
			Error::AddError($SQLError);
			return false;
		}
	}

	/**
	 * @param string $Table
	 * @param array() $Rows
	 * @param array() $Where
	 * @return bool, TRUE on success, FALSE on failure
	 */
	public function Update($Table, $Rows, $Where)
	{
		// Thanks to http://net.tutsplus.com/
		if($this->TableExists($Table))
		{
			$NewWhere = array();
			
			for($i = 0; $i < count($Where); $i++)
			{
				if($i % 2 == 0)
				{
					if(is_string($Where[$i+1]))
					{ $NewWhere[] = $Where[$i].' = "'.$Where[$i+1].'"'; }
					else
					{ $NewWhere[] = $Where[$i].' = '.$Where[$i+1]; }
				}
			}
			
			$NewWhere = implode(' AND ', $NewWhere);

			$Update = 'UPDATE `'.$Table.'` SET ';
			$Keys = array_keys($Rows);

			for($i = 0; $i < count($Rows); $i++)
			{
				if(is_null($Rows[$Keys[$i]]) || strlen($Rows[$Keys[$i]]) == 0)
				{ $Update .= $Keys[$i].' = NULL'; }
				else if(is_string($Rows[$Keys[$i]]))
				{ $Update .= $Keys[$i].' = "'.$Rows[$Keys[$i]].'"'; }
				else
				{ $Update .= $Keys[$i].' = '.$Rows[$Keys[$i]]; }

				if($i != count($Rows)-1)
				{ $Update .= ','; }
			}
			$Update .= ' WHERE '.$NewWhere;

			$query = @mysql_query($Update);

			if($query)
			{ return true; }
			else
			{
				$SQLError = new SQLerror();
				$SQLError->setErrorNumber(mysql_errno());
				$SQLError->setErrorMessage(mysql_error());
				Error::AddError($SQLError);
				return false;
			}
		}
		else
		{
			$SQLError = new SQLerror();
			$SQLError->setErrorNumber(SQL_ERR_NOSUCHTABLE);
			$SQLError->setErrorMessage(SQLerror::TranslateSQLError(SQL_ERR_NOSUCHTABLE));
			Error::AddError($SQLError);
			return false;
		}
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
	 * @param array $FieldInfo
	 * @param string $NameToFind
	 * @return string
	 */
	private static function FindType($FieldInfo, $NameToFind)
	{
		foreach($FieldInfo as $Key => $Value)
		{
			if($Value->name == $NameToFind)
			{ return $Value->type; }
		}
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