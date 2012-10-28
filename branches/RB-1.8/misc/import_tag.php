<?php

/* This file needs to be put in the application's root directory */

include('cd.php');
ini_set('max_execution_time', '3600');
$CurrentUser = Authentication::Authenticate();
$TagsInDb = Tag::GetTags();

function NotComment($line){
	return !preg_match('/^\s*;/i', $line);
}

if(isset($argv) && $argc > 0)
{ $f = sprintf('%1$s/misc/tag_list.txt', dirname($_SERVER['PHP_SELF'])); }
else
{ $f = 'misc/tag_list.txt'; }


$fc = file_get_contents($f);
$lines = preg_split('/\n/i', $fc, NULL, PREG_SPLIT_NO_EMPTY);
$lines = array_filter($lines, 'NotComment');

Tag2All::HandleTags($lines, array(), $TagsInDb, $CurrentUser);

?>