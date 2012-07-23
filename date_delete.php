<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();

if($CurrentUser->hasPermission(RIGHT_SET_ADD) || $CurrentUser->hasPermission(RIGHT_SET_EDIT))
{
	$DateID = Utils::SafeIntFromQS('date_id');
	
	if(isset($DateID))
	{
		Date::DeleteDate(new Date($DateID), $CurrentUser);
	}
}
else
{
	$e = new Error(RIGHTS_ERR_USERNOTALLOWED);
	Error::AddError($e);
}

HTMLstuff::RefererRedirect();

?>