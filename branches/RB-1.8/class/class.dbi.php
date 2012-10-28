<?php

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