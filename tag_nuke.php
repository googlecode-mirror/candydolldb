<?php

include('cd.php');
ini_set('max_execution_time', '3600');
$CurrentUser = Authentication::Authenticate();

if($CurrentUser->hasPermission(RIGHT_TAG_CLEANUP))
{
	$Tags = Tag::GetTags();
	
	/* @var $t Tag */
	foreach($Tags as $t)
	{
		$t2as = Tag2All::GetTag2Alls(new Tag2AllSearchParameters($t->getID()));
		
		if(!$t2as){
			Tag::Delete($t, $CurrentUser);
		}
	}
	
	$infoSuccess = new Info($lang->g('MessageTagsCleaned'));
	Info::AddInfo($infoSuccess);
}
else
{
	$e = new Error(RIGHTS_ERR_USERNOTALLOWED);
	Error::AddError($e);
}

HTMLstuff::RefererRedirect();

?>