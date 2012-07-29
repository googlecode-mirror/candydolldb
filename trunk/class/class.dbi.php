<?php

class DBi extends mysqli
{
}

class SearchParameters
{
	private $paramtypes = '';
	private $values = array();
	private $where = '';
	
	public function __construct()
	{
	}
	
	public function getWhere()
	{
		return $this->where;
	}
	
	public function getValues()
	{
		return $this->values;
	}
	
	public function getParamTypes()
	{
		return $this->paramtypes;
	}
}

?>