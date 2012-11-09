<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');
date_default_timezone_set(@date_default_timezone_get());

include('class'.DIRECTORY_SEPARATOR.'class.i18n.php');
include('class'.DIRECTORY_SEPARATOR.'class.i18n.en.php');
include('class'.DIRECTORY_SEPARATOR.'class.i18n.nl.php');
include('class'.DIRECTORY_SEPARATOR.'class.i18n.de.php');

$lang = new i18n();

if(isset($argv) && $argc > 0)
{
	$configPath = sprintf('%1$s%2$sconfig.php', dirname($_SERVER['PHP_SELF']), DIRECTORY_SEPARATOR);
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
   !defined('DBPASSWORD'))
{
	if(!array_key_exists('REQUEST_URI', $_SERVER))
	{
		echo $lang->g('ErrorPleaseUseWebInterfaceForSetup')."\n";
		exit(128);
	}
	else if(stristr(basename($_SERVER['REQUEST_URI']), 'setup') === FALSE)
	{
		header('location:setup.php');
		exit(128);
	}
}

if(!defined('DBNAME'))
{ define('DBNAME', 'candydolldb'); }

define('CANDYDOLLDB_VERSION', '1.9');

/* Rights */
	define('RIGHT_ACCOUNT_LOGIN',	1);
	define('RIGHT_ACCOUNT_EDIT',	2);
	define('RIGHT_ACCOUNT_PASSWORD',3);
	
	define('RIGHT_MODEL_ADD',		4);
	define('RIGHT_MODEL_EDIT',		5);
	define('RIGHT_MODEL_DELETE',	6);
	
	define('RIGHT_SET_ADD',			7);
	define('RIGHT_SET_EDIT',		8);
	define('RIGHT_SET_DELETE',		9);
	
	define('RIGHT_IMAGE_ADD',		10);
	define('RIGHT_IMAGE_EDIT',		11);
	define('RIGHT_IMAGE_DELETE',	12);
	
	define('RIGHT_VIDEO_ADD',		13);
	define('RIGHT_VIDEO_EDIT',		14);
	define('RIGHT_VIDEO_DELETE',	15);
	
	define('RIGHT_TAG_ADD',			16);
	define('RIGHT_TAG_EDIT',		17);
	define('RIGHT_TAG_DELETE',		18);
	
	define('RIGHT_USER_ADD',		19);
	define('RIGHT_USER_EDIT',		20);
	define('RIGHT_USER_DELETE',		21);
	define('RIGHT_USER_RIGHTS',		22);
	
	define('RIGHT_IMPORT_XML',		23);
	define('RIGHT_EXPORT_XML',		24);
	
	define('RIGHT_EXPORT_ZIP',		25);
	define('RIGHT_EXPORT_ZIP_MULTI',26);
	
	define('RIGHT_EXPORT_SFV',		27);
	define('RIGHT_EXPORT_CSV',		28);
	define('RIGHT_EXPORT_INDEX',	29);
	define('RIGHT_EXPORT_VIDEO',	30);
	
	define('RIGHT_SEARCH_DIRTY',	31);
	define('RIGHT_SEARCH_TAGS',		32);
	
	define('RIGHT_CACHE_CLEANUP',	33);
	define('RIGHT_TAG_CLEANUP',		34);
	define('RIGHT_CACHE_DELETE',	35);
	define('RIGHT_CONFIG_REWRITE',	36);
/* /Rights */
	
define('GENDER_UNKNOWN', 0);
define('GENDER_FEMALE', 1);
define('GENDER_MALE', 2);

define('SET_CONTENT_NONE',  0);
define('SET_CONTENT_IMAGE', 1);
define('SET_CONTENT_VIDEO', 2);

define('DATE_KIND_UNKNOWN',  0);
define('DATE_KIND_IMAGE', 1);
define('DATE_KIND_VIDEO', 2);

define('EXPORT_PATH_OPTION_NONE', 0);
define('EXPORT_PATH_OPTION_RELATIVE', 1);
define('EXPORT_PATH_OPTION_FULL',  2);

define('EXPORT_OPTION_SERIALIZE', 0);
define('EXPORT_OPTION_IMAGE', 1);
define('EXPORT_OPTION_ZIP', 2);

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

$CSVRegex = '/\s*(?<!\\\),\s*/';
$DateStyleArray = array(
	"d-m-Y",	// 14-04-2012
	"j-n-Y",	// 14-4-2012
	"j F Y",	// 14 April 2012 
	"m-d-Y",	// 04-14-2012 
	"n-j-Y",	// 4-14-2012 
	"F j, Y"	// April 14, 2012 
);

include('class'.DIRECTORY_SEPARATOR.'class.global.php');
include('class'.DIRECTORY_SEPARATOR.'class.error.php');
include('class'.DIRECTORY_SEPARATOR.'class.info.php');
include('class'.DIRECTORY_SEPARATOR.'class.html.php');
include('class'.DIRECTORY_SEPARATOR.'class.dbi.php');

@session_start();

if(!array_key_exists('Errors', $_SESSION))
{ $_SESSION['Errors'] = serialize(array()); }

if(!array_key_exists('Infos', $_SESSION))
{ $_SESSION['Infos'] = serialize(array()); }

if(defined('DBHOSTNAME') &&
   defined('DBUSERNAME') &&
   defined('DBPASSWORD'))
{
	$dbi = @new DBi(DBHOSTNAME, DBUSERNAME, DBPASSWORD, DBNAME);
	if(mysqli_connect_errno() == 0)
	{
		$dbi->query("SET GLOBAL sql_mode = 'STRICT_ALL_TABLES';");
	}
	else
	{
		printf($lang->g('ErrorSetupConnectDatabase'), mysqli_connect_error());
		exit;
	}
}

include('class'.DIRECTORY_SEPARATOR.'class.user.php');
include('class'.DIRECTORY_SEPARATOR.'class.date.php');
include('class'.DIRECTORY_SEPARATOR.'class.image.php');
include('class'.DIRECTORY_SEPARATOR.'class.cacheimage.php');
include('class'.DIRECTORY_SEPARATOR.'class.model.php');
include('class'.DIRECTORY_SEPARATOR.'class.set.php');
include('class'.DIRECTORY_SEPARATOR.'class.video.php');
include('class'.DIRECTORY_SEPARATOR.'class.tag.php');
include('class'.DIRECTORY_SEPARATOR.'class.tag2all.php');
include('class'.DIRECTORY_SEPARATOR.'class.indexgenerator.php');

$EmailPages = array('password.php');
if(in_array(basename($_SERVER['PHP_SELF']), $EmailPages))
{
	require 'class'.DIRECTORY_SEPARATOR.'class.phpmailer.php';
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