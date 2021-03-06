<?php

include('cd.php');

$Tags = Tag::GetTags();
$a = array();
$q = NULL;
$o = '';

if(array_key_exists('q', $_POST) && strlen($_POST['q']) > 0)
{
	$q = array_pop(
		preg_split(
			$CSVRegex,
			$_POST['q'].',',
			NULL,
			PREG_SPLIT_NO_EMPTY
		)
	);
}

if(!is_null($q))
{
	foreach ($Tags as $t)
	{
		if(stripos($t->getName(), $q) !== FALSE)
		{
			$a[] = $t;
		}
	}

	foreach ($a as $t)
	{
		$o .= sprintf('<a href="#" style="display:block">%1$s</a>',
			str_ireplace($q, '<strong>'.htmlentities($q).'</strong>', $t->getName())
		);
	}

	echo $o;
}

?>