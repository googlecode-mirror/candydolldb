<?php

include('cd.php');

$Tags = Tag::GetTags();
$a = array();
$q = null;
$o = '';

if(array_key_exists('q', $_POST) && strlen($_POST['q']) > 0)
{
	$q = array_pop(
		preg_split(
			$CSVRegex,
			$_POST['q'].',',
			null,
			PREG_SPLIT_NO_EMPTY
		)
	);
}

if(!is_null($q))
{
	foreach ($Tags as $t)
	{
		if(stripos($t->getName(), $q) !== false)
		{
			$a[] = $t;
		}
	}

	foreach ($a as $t)
	{
		$o .= sprintf('<a href="#">%1$s</a><br />',
			str_ireplace($q, '<strong>'.htmlentities($q).'</strong>', $t->getName())
		);
	}

	echo $o;
}

?>