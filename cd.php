<?php

error_reporting(E_ALL);

if($argv && $argc > 0)
{
	// On the commandline, include using absolute path
	if(file_exists(sprintf('%1$s/config.php', dirname($_SERVER['PHP_SELF']))))
	{ require_once(sprintf('%1$s/config.php', dirname($_SERVER['PHP_SELF']))); }
}
else
{
	// During a HTTP-request, include using relative path
	if(file_exists('config.php'))
	{ require_once('config.php'); }
}

if(!defined('DBHOSTNAME') || strlen(DBHOSTNAME) == 0 ||
   !defined('DBUSERNAME') || strlen(DBUSERNAME) == 0 ||
   !defined('DBPASSWORD') || strlen(DBPASSWORD) == 0)
{
	header('location:setup.php');
	exit(128);
}

define('GENDER_UNKNOWN', 0);
define('GENDER_FEMALE', 1);
define('GENDER_MALE', 2);

define('SET_CONTENT_NONE',  0);
define('SET_CONTENT_IMAGE', 1);
define('SET_CONTENT_VIDEO', 2);

define('COMMAND_DELETE', 'del');
define('IMAGE_EXTENSION', '.jpg');
define('VIDEO_EXTENSION', '.wmv');

define('LOGIN_ERR_PASSWORDSNOTIDENTICAL', 33362);
define('LOGIN_ERR_RESETCODENOTFOUND', 33363);
define('LOGIN_ERR_USERNAMENOTFOUND', 33364);
define('LOGIN_ERR_PASSWORDINCORRECT', 33365);
define('SQL_ERR_NOSUCHTABLE', 33366);
define('SYNTAX_ERR_EMAILADDRESS', 33367);

define('RIGHT_LOGIN',			1);
define('RIGHT_DOWNLOAD_ZIP', 	2);
define('RIGHT_DOWNLOAD_VID',	4);
define('RIGHT_IMPORT_ZIP',		8);
define('RIGHT_IMPORT_VID',		16);
define('RIGHT_MANAGE_MODEL', 	32);
define('RIGHT_MANAGE_SET',   	64);
define('RIGHT_MANAGE_IMAGE', 	128);
define('RIGHT_MANAGE_VIDEO', 	256);

include('class/class.global.php');
include('class/class.error.php');
include('class/class.html.php');
include('class/class.db.php');

@session_start();

if(!array_key_exists('Errors', $_SESSION))
{ $_SESSION['Errors'] = serialize(array()); }

$db = new DB(DBHOSTNAME, DBUSERNAME, DBPASSWORD);
$db->Connect();
$db->setDatabaseName('cdtvdb');

include('class/class.user.php');
include('class/class.image.php');
include('class/class.model.php');
include('class/class.set.php');
include('class/class.video.php');

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