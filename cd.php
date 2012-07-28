<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');
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

define('CANDYDOLLDB_VERSION', '1.8');

define('RIGHT_ACCOUNT_LOGIN',	1);
define('RIGHT_ACCOUNT_EDIT',	2);
define('RIGHT_ACCOUNT_PASSWORD',4);
define('RIGHT_IMPORT_XML',		8);
define('RIGHT_EXPORT_XML',		16);
define('RIGHT_MODEL_ADD',		32);
define('RIGHT_MODEL_EDIT',		64);
define('RIGHT_MODEL_DELETE',	128);
define('RIGHT_SET_ADD',			256);
define('RIGHT_SET_EDIT',		512);
define('RIGHT_SET_DELETE',		1024);
define('RIGHT_IMAGE_ADD',		2048);
define('RIGHT_IMAGE_EDIT',		4096);
define('RIGHT_IMAGE_DELETE',	8192);
define('RIGHT_VIDEO_ADD',		16384);
define('RIGHT_VIDEO_EDIT',		32768);
define('RIGHT_VIDEO_DELETE',	65536);
define('RIGHT_TAG_ADD',			131072);
define('RIGHT_TAG_EDIT',		262144);
define('RIGHT_TAG_DELETE',		524288);
define('RIGHT_USER_ADD',		1048576);
define('RIGHT_USER_EDIT',		2097152);
define('RIGHT_USER_DELETE',		4194304);
define('RIGHT_USER_RIGHTS',		8388608);
define('RIGHT_EXPORT_ZIP',		16777216);
define('RIGHT_EXPORT_ZIP_MULTI',33554432);
define('RIGHT_EXPORT_INDEX',	67108864);
define('RIGHT_SEARCH_TAG',		134217728);
define('RIGHT_SEARCH_DIRTY',	268435456);
define('RIGHT_CACHE_CLEANUP',	536870912);
define('RIGHT_TAG_CLEANUP',		1073741824);
define('RIGHT_CACHE_DELETE',	2147483648);

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
define('XML_ERR_XML_VALID', 33369);
define('XML_ERR_SCHEMA_VALID', 33370);
define('RIGHTS_ERR_USERNOTALLOWED', 33371);

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
include('class/class.info.php');
include('class/class.html.php');
include('class/class.db.php');
include('class/class.dbi.php');

@session_start();

if(!array_key_exists('Errors', $_SESSION))
{ $_SESSION['Errors'] = serialize(array()); }

if(!array_key_exists('Infos', $_SESSION))
{ $_SESSION['Infos'] = serialize(array()); }

if(defined('DBHOSTNAME') &&
   defined('DBUSERNAME') &&
   defined('DBPASSWORD'))
{
	$db = new DB(DBHOSTNAME, DBUSERNAME, DBPASSWORD);
	$db->Connect();
	$db->setDatabaseName(DBNAME);
	
	$dbi = new DBi(DBHOSTNAME, DBUSERNAME, DBPASSWORD, DBNAME);
	$dbi->query("SET GLOBAL sql_mode = 'STRICT_ALL_TABLES';");
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