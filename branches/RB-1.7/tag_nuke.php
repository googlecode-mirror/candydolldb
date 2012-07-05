<?php

include('cd.php');
ini_set('max_execution_time', '3600');
$CurrentUser = Authentication::Authenticate();

$Tags = Tag::GetTags();
$Tag2Alls = Tag2All::GetTag2Alls();

/* @var $t Tag */
/* @var $t2a Tag2All */
foreach($Tags as $t)
{
	$t2a = Tag2All::FilterTag2Alls($Tag2Alls, $t->getID(), FALSE, FALSE, FALSE, FALSE);
	
	if(!$t2a){
		Tag::DeleteTag($t, $CurrentUser);
	}
}

HTMLstuff::RefererRedirect();

?>