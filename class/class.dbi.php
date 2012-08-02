<?php

class DBi extends mysqli
{
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