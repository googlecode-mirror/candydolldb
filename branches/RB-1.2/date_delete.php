<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();

$DateID = null;

if(array_key_exists('date_id', $_GET) && isset($_GET['date_id']) && is_numeric($_GET['date_id']))
{
	$DateID = (int)$_GET['date_id'];
	Date::DeleteDate(new Date($DateID), $CurrentUser);
}

HTMLstuff::RefererRedirect();

?>