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
