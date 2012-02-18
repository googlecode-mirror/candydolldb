<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();

$DateID = Utils::SafeIntFromQS('date_id');

if(isset($DateID))
{
	Date::DeleteDate(new Date($DateID), $CurrentUser);
}

HTMLstuff::RefererRedirect();

?>