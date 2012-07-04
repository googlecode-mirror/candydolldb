<?php

error_reporting(E_ALL);
date_default_timezone_set(@date_default_timezone_get());


include('class/class.i18n.php');
include('class/class.i18n.en.php');
include('class/class.i18n.nl.php');
include('class/class.i18n.de.php');

$lang = new i18n();

if(isset($argv) && $argc > 0)
{
	$configPath = sprintf('%1$s/config.php', dirname($_SERVER['PHP_SELF']));
	if(file_exists($configPath))
	{ require_once($configPath); }
}
else
{
	if(file_exists('config.php'))
	{ require_once('config.php'); }
}

if(!defined('DBHOSTNAME') || strlen(DBHOSTNAME) == 0 ||
   !defined('DBUSERNAME') || strlen(DBUSERNAME) == 0 ||
   !defined('DBNAME') || strlen(DBNAME) == 0 ||
   !defined('DBPASSWORD'))
{
	if(!array_key_exists('REQUEST_URI', $_SERVER))
	{
		echo $lang->g('ErrorPleaseUseWebInterfaceForSetup')."\n";
		exit(128);
	}
	else if(basename($_SERVER['REQUEST_URI']) != 'setup.php')
	{
		header('location:setup.php');
		exit(128);
	}
	
	if(!defined('DBNAME'))
	{ define('DBNAME', 'candydolldb'); }
}

define('CANDYDOLLDB_VERSION', '1.7');

define('GENDER_UNKNOWN', 0);
define('GENDER_FEMALE', 1);
define('GENDER_MALE', 2);

define('SET_CONTENT_NONE',  0);
define('SET_CONTENT_IMAGE', 1);
define('SET_CONTENT_VIDEO', 2);

define('DATE_KIND_UNKNOWN',  0);
define('DATE_KIND_IMAGE', 1);
define('DATE_KIND_VIDEO', 2);

define('CACHEIMAGE_KIND_UNKNOWN', 0);
define('CACHEIMAGE_KIND_IMAGE', 1);
define('CACHEIMAGE_KIND_VIDEO', 2);
define('CACHEIMAGE_KIND_SET',	4);
define('CACHEIMAGE_KIND_INDEX', 8);
define('CACHEIMAGE_KIND_MODEL', 16);

define('COMMAND_DELETE', 'del');

define('LOGIN_ERR_PASSWORDSNOTIDENTICAL', 33362);
define('LOGIN_ERR_USERNAMEANDMAILADDRESNOTFOUND', 33369);
define('LOGIN_ERR_RESETCODENOTFOUND', 33363);
define('LOGIN_ERR_USERNAMENOTFOUND', 33364);
define('LOGIN_ERR_PASSWORDINCORRECT', 33365);
define('SQL_ERR_NOSUCHTABLE', 33366);
define('SYNTAX_ERR_EMAILADDRESS', 33367);
define('REQUIRED_FIELD_MISSING', 33368);

$SplitRegex = "/;+(?=([^'|^\\\']*['|\\\'][^'|^\\\']*['|\\\'])*[^'|^\\\']*[^'|^\\\']$)/";
$CSVRegex = '/\s*(?<!\\\),\s*/';
$DateStyleArray = array(
	"d-m-Y",	// 14-04-2012
	"j-n-Y",	// 14-4-2012
	"j F Y",	// 14 April 2012 
	"m-d-Y",	// 04-14-2012 
	"n-j-Y",	// 4-14-2012 
	"F j, Y"	// April 14, 2012 
);

include('class/class.global.php');
include('class/class.error.php');
include('class/class.html.php');
include('class/class.db.php');

@session_start();

if(!array_key_exists('Errors', $_SESSION))
{ $_SESSION['Errors'] = serialize(array()); }

if(defined('DBHOSTNAME') &&
   defined('DBUSERNAME') &&
   defined('DBPASSWORD'))
{
	$db = new DB(DBHOSTNAME, DBUSERNAME, DBPASSWORD);
	$db->Connect();
	$db->setDatabaseName(DBNAME);
}

include('class/class.user.php');
include('class/class.date.php');
include('class/class.image.php');
include('class/class.cacheimage.php');
include('class/class.model.php');
include('class/class.set.php');
include('class/class.video.php');
include('class/class.tag.php');
include('class/class.tag2all.php');
include('class/class.search.php');

$EmailPages = array('password.php');
if(in_array(basename($_SERVER['PHP_SELF']), $EmailPages))
{
	require 'class/class.phpmailer.php';
	$ml = new PHPMailer();
	$ml->IsSMTP();
	$ml->From = SMTP_FROM_ADDRESS;
	$ml->FromName = SMTP_FROM_NAME;
	$ml->Host = SMTP_HOST;
	$ml->Username = SMTP_USERNAME;
	$ml->Password = SMTP_PASSWORD;
	$ml->Port = SMTP_PORT;
	$ml->SMTPAuth = SMTP_AUTH;
}

?>