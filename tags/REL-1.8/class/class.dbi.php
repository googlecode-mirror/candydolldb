<?php
/* This file is part of CandyDollDB.

CandyDollDB is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

CandyDollDB is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with CandyDollDB. If not, see <http://www.gnu.org/licenses/>.
*/

class DBi extends mysqli
{
	/**
	 * Exexutes the supplied queries.
 	 * @param string $q
 	 * @return bool
	 */
	public function ExecuteMulti($q)
	{
		$this->autocommit(FALSE);
		if($this->multi_query($q))
		{
			do
			{ ; }
			while($this->next_result());
			
			$this->autocommit(TRUE);
			return TRUE;
		}
		
		$e = new Error($this->errno, $this->error);
		Error::AddError($e);
		return FALSE;
	}
	
	/**
	 * Returns whether the specified column exists.
	 * @param string $TableName
	 * @param string $ColumnName
	 * @return bool
	 */
	public function ColumnExists($TableName, $ColumnName)
	{
		$q = sprintf("SHOW COLUMNS FROM `%1\$s` LIKE '%2\$s';",
			$this->escape_string($TableName),
			$this->escape_string($ColumnName));

		/* @var $r mysqli_result */
		if($r = $this->query($q))
		{
			return $r->fetch_assoc() ? TRUE : FALSE;
		}
		
		return FALSE;
	}
	
	/**
	 * Bind the given SearchParameter values to the given SELECT-statement. 
	 * @param SearchParameters $SearchParameters
	 * @param mysqli_stmt $stmt 
	 */
	public static function BindParamsToSelect(&$SearchParameters, &$stmt)
	{
		if($SearchParameters->getValues())
		{
			$bind_names[] = $SearchParameters->getParamTypes();
			$params = $SearchParameters->getValues();
			
			for ($i=0; $i<count($params);$i++)
			{
				$bind_name = 'bind' . $i;
				$$bind_name = $params[$i];
				$bind_names[] = &$$bind_name;
			}
			call_user_func_array(array($stmt, 'bind_param'), $bind_names);
		}
	}
	
	/**
	 * Bind the given Tag2All-values to the given DELETE-statement.
	 * @param Tag2All $Tag2All
	 * @param mysqli_stkmt $stmt
	 */
	public static function BindParamsToDeleteT2A(&$Tag2All, &$stmt)
	{
		if($Tag2All->getDeleteBindValues())
		{
			$bind_names[] = $Tag2All->getDeleteBindPattern();
			$params = $Tag2All->getDeleteBindValues();
			
			for ($i=0; $i<count($params);$i++)
			{
				$bind_name = 'bind' . $i;
				$$bind_name = $params[$i];
				$bind_names[] = &$$bind_name;
			}
			call_user_func_array(array($stmt, 'bind_param'), $bind_names);
		}
	}
}

class SearchParameters
{
	public function __construct()
	{
	}
}

?>
