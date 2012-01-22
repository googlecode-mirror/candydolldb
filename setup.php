<?php

require_once('cd.php');

function BackToThisPage($Text)
{ return sprintf('<a href="%2$s">%1$s</a>', $Text, $_SERVER['REQUEST_URI']); }

function ExecuteQueries($SQL)
{
	// Thanks to http://www.dev-explorer.com/articles/multiple-mysql-queries
	$splitregex = "/;+(?=([^'|^\\\']*['|\\\'][^'|^\\\']*['|\\\'])*[^'|^\\\']*[^'|^\\\']$)/";
	$queries = preg_split($splitregex, $SQL);

	$OutBool = true;
	foreach($queries as $q)
	{	
		if(strlen(trim($q)) == 0) { continue; }
		$OutBool = @mysql_query($q);
		if($OutBool === false) { break; }
	}
	return $OutBool;
}


if(file_exists('config.php'))
{ die(sprintf('Setup already complete, please remove \'config.php\' from your installation directory and %1$s.', BackToThisPage('revisit this page'))); }


$DBHostName = null;
$DBUserName = null;
$DBPassword = null;

$UserName = null;
$Password = null;
$UserFirstName = null;
$UserLastName = null;
$UserEmail = null;

$CandyImagePath = null;
$CandyVideoPath = null;
$CandyVideoThumbPath = null;

$SmtpFromAddress = null;
$SmtpFromName = null;
$SmtpHostname = null;
$SmtpUsername = null;
$SmtpPassword = null;
$SmtpPort = 0;
$SmtpAuth = false;


$InsertUserSQL = <<<FjbMNnvUJheiwewUJfheJheuehFJDUHdywgwwgHGfgywug
INSERT INTO `User` (
  `user_username`,
  `user_password`,
  `user_salt`,
  `user_firstname`,
  `user_lastname`,
  `user_email`,
  `mut_id`,
  `mut_date`,
  `mut_deleted`
) VALUES (
	'%1\$s',
	'%2\$s',
	'%3\$s',
	'%4\$s',
	'%5\$s',
	'%6\$s',
	1,
	UNIX_TIMESTAMP(),
	-1
);
FjbMNnvUJheiwewUJfheJheuehFJDUHdywgwwgHGfgywug;


$CreateDBSQL = <<<FjbMNnvUJheiwewUJfheJheuehFJDUHdywgwwgHGfgywug
SET AUTOCOMMIT=0;
START TRANSACTION;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

DROP DATABASE IF EXISTS `cdtvdb`;
DROP DATABASE IF EXISTS `candydolldb`;
CREATE DATABASE `candydolldb` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `candydolldb`;

DROP TABLE IF EXISTS `Date`;
CREATE TABLE IF NOT EXISTS `Date` (
  `date_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `set_id` bigint(20) NOT NULL,
  `date_kind` tinyint(4) NOT NULL DEFAULT '0',
  `date_timestamp` bigint(20) NOT NULL DEFAULT '-1',
  `mut_id` bigint(20) NOT NULL,
  `mut_date` bigint(20) NOT NULL DEFAULT '-1',
  `mut_deleted` bigint(20) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`date_id`),
  UNIQUE KEY `UNIQ_DATE` (`mut_deleted`,`set_id`,`date_kind`,`date_timestamp`),
  KEY `set_id` (`set_id`),
  KEY `mut_id` (`mut_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `Image`;
CREATE TABLE IF NOT EXISTS `Image` (
  `image_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `set_id` bigint(20) NOT NULL,
  `image_filename` varchar(100) NOT NULL,
  `image_fileextension` varchar(10) NOT NULL,
  `image_filesize` bigint(20) NOT NULL DEFAULT '0',
  `image_filechecksum` varchar(32) DEFAULT NULL,
  `image_width` int(11) NOT NULL DEFAULT '0',
  `image_height` int(11) NOT NULL DEFAULT '0',
  `mut_id` bigint(20) NOT NULL,
  `mut_date` bigint(20) NOT NULL DEFAULT '-1',
  `mut_deleted` bigint(20) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`image_id`),
  UNIQUE KEY `UNIQ_IMAGE` (`mut_deleted`,`set_id`,`image_filename`,`image_fileextension`),
  KEY `set_id` (`set_id`),
  KEY `mut_deleted` (`mut_deleted`),
  KEY `mut_id` (`mut_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `Model`;
CREATE TABLE IF NOT EXISTS `Model` (
  `model_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `model_firstname` varchar(100) NOT NULL,
  `model_lastname` varchar(100) DEFAULT NULL,
  `model_birthdate` bigint(20) NOT NULL DEFAULT '-1',
  `mut_id` bigint(20) NOT NULL,
  `mut_date` bigint(20) NOT NULL,
  `mut_deleted` bigint(20) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`model_id`),
  UNIQUE KEY `UNIQ_MODEL` (`mut_deleted`,`model_firstname`,`model_lastname`),
  KEY `mut_deleted` (`mut_deleted`),
  KEY `mut_id` (`mut_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `Set`;
CREATE TABLE IF NOT EXISTS `Set` (
  `set_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `model_id` bigint(20) NOT NULL,
  `set_prefix` varchar(50) DEFAULT NULL,
  `set_name` varchar(100) NOT NULL,
  `set_containswhat` tinyint(4) NOT NULL DEFAULT '3',
  `mut_id` bigint(20) NOT NULL,
  `mut_date` bigint(20) NOT NULL,
  `mut_deleted` bigint(20) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`set_id`),
  UNIQUE KEY `UNIQ_SET` (`mut_deleted`,`model_id`,`set_prefix`,`set_name`),
  KEY `model_id` (`model_id`),
  KEY `mut_deleted` (`mut_deleted`),
  KEY `mut_id` (`mut_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `User`;
CREATE TABLE IF NOT EXISTS `User` (
  `user_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_username` varchar(50) NOT NULL,
  `user_password` varchar(128) NOT NULL,
  `user_salt` varchar(20) NOT NULL,
  `user_firstname` varchar(50) NOT NULL,
  `user_insertion` varchar(20) DEFAULT NULL,
  `user_lastname` varchar(100) NOT NULL,
  `user_email` varchar(253) NOT NULL,
  `user_gender` tinyint(4) NOT NULL DEFAULT '0',
  `user_birthdate` bigint(20) NOT NULL DEFAULT '-1',
  `user_lastactive` bigint(20) NOT NULL DEFAULT '-1',
  `user_lastlogin` bigint(20) NOT NULL DEFAULT '-1',
  `user_prelastlogin` bigint(20) NOT NULL DEFAULT '-1',
  `user_rights` bigint(20) NOT NULL DEFAULT '0',
  `mut_id` bigint(20) NOT NULL,
  `mut_date` bigint(20) NOT NULL DEFAULT '-1',
  `mut_deleted` bigint(20) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `UNIQ_USER` (`mut_deleted`,`user_username`),
  KEY `mut_id` (`mut_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `Video`;
CREATE TABLE IF NOT EXISTS `Video` (
  `video_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `set_id` bigint(20) NOT NULL,
  `video_filename` varchar(100) NOT NULL,
  `video_fileextension` varchar(10) NOT NULL,
  `video_filesize` bigint(20) NOT NULL DEFAULT '0',
  `video_filechecksum` varchar(32) DEFAULT NULL,
  `mut_id` bigint(20) NOT NULL,
  `mut_date` bigint(20) NOT NULL,
  `mut_deleted` bigint(20) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`video_id`),
  UNIQUE KEY `UNIQ_VIDEO` (`mut_deleted`,`set_id`,`video_filename`,`video_fileextension`),
  KEY `set_id` (`set_id`),
  KEY `mut_deleted` (`mut_deleted`),
  KEY `mut_id` (`mut_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `CacheImage`;
CREATE TABLE IF NOT EXISTS `CacheImage` (
  `cache_id` varchar(36) NOT NULL,
  `model_id` bigint(20) DEFAULT NULL,
  `index_id` bigint(20) DEFAULT NULL,
  `set_id` bigint(20) DEFAULT NULL,
  `image_id` bigint(20) DEFAULT NULL,
  `video_id` bigint(20) DEFAULT NULL,
  `cache_imagewidth` int(11) NOT NULL DEFAULT '0',
  `cache_imageheight` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cache_id`),
  KEY `model_id` (`model_id`),
  KEY `index_id` (`index_id`),
  KEY `set_id` (`set_id`),
  KEY `image_id` (`image_id`),
  KEY `video_id` (`video_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP VIEW IF EXISTS `vw_Date`;
CREATE TABLE IF NOT EXISTS `vw_Date` (
`date_id` bigint(20)
,`date_kind` tinyint(4)
,`date_timestamp` bigint(20)
,`mut_deleted` bigint(20)
,`set_id` bigint(20)
,`set_prefix` varchar(50)
,`set_name` varchar(100)
,`set_containswhat` tinyint(4)
,`model_id` bigint(20)
,`model_firstname` varchar(100)
,`model_lastname` varchar(100)
);

DROP VIEW IF EXISTS `vw_Image`;
CREATE TABLE IF NOT EXISTS `vw_Image` (
`image_id` bigint(20)
,`image_filename` varchar(100)
,`image_fileextension` varchar(10)
,`image_filesize` bigint(20)
,`image_filechecksum` varchar(32)
,`image_width` int(11)
,`image_height` int(11)
,`mut_deleted` bigint(20)
,`set_id` bigint(20)
,`set_prefix` varchar(50)
,`set_name` varchar(100)
,`set_containswhat` tinyint(4)
,`model_id` bigint(20)
,`model_firstname` varchar(100)
,`model_lastname` varchar(100)
);

DROP VIEW IF EXISTS `vw_Model`;
CREATE TABLE IF NOT EXISTS `vw_Model` (
`model_id` bigint(20)
,`model_firstname` varchar(100)
,`model_lastname` varchar(100)
,`model_birthdate` bigint(20)
,`mut_deleted` bigint(20)
,`model_setcount` bigint(21)
);

DROP VIEW IF EXISTS `vw_Set`;
CREATE TABLE IF NOT EXISTS `vw_Set` (
`set_id` bigint(20)
,`set_prefix` varchar(50)
,`set_name` varchar(100)
,`set_containswhat` tinyint(4)
,`mut_deleted` bigint(20)
,`model_id` bigint(20)
,`model_firstname` varchar(100)
,`model_lastname` varchar(100)
,`set_amount_pics_in_db` bigint(21)
,`set_amount_vids_in_db` bigint(21)
);

DROP VIEW IF EXISTS `vw_Video`;
CREATE TABLE IF NOT EXISTS `vw_Video` (
`video_id` bigint(20)
,`video_filename` varchar(100)
,`video_fileextension` varchar(10)
,`video_filesize` bigint(20)
,`video_filechecksum` varchar(32)
,`mut_deleted` bigint(20)
,`set_id` bigint(20)
,`set_prefix` varchar(50)
,`set_name` varchar(100)
,`set_containswhat` tinyint(4)
,`model_id` bigint(20)
,`model_firstname` varchar(100)
,`model_lastname` varchar(100)
);

DROP TABLE IF EXISTS `vw_Date`;
CREATE ALGORITHM=UNDEFINED VIEW `vw_Date` AS select `Date`.`date_id` AS `date_id`,`Date`.`date_kind` AS `date_kind`,`Date`.`date_timestamp` AS `date_timestamp`,`Date`.`mut_deleted` AS `mut_deleted`,`Date`.`set_id` AS `set_id`,`Set`.`set_prefix` AS `set_prefix`,`Set`.`set_name` AS `set_name`,`Set`.`set_containswhat` AS `set_containswhat`,`Model`.`model_id` AS `model_id`,`Model`.`model_firstname` AS `model_firstname`,`Model`.`model_lastname` AS `model_lastname` from ((`Date` left join `Set` on((`Set`.`set_id` = `Date`.`set_id`))) left join `Model` on((`Model`.`model_id` = `Set`.`model_id`))) where ((`Set`.`mut_deleted` = -(1)) and (`Model`.`mut_deleted` = -(1)));

DROP TABLE IF EXISTS `vw_Image`;
CREATE ALGORITHM=UNDEFINED VIEW `vw_Image` AS select `Image`.`image_id` AS `image_id`,`Image`.`image_filename` AS `image_filename`,`Image`.`image_fileextension` AS `image_fileextension`,`Image`.`image_filesize` AS `image_filesize`,`Image`.`image_filechecksum` AS `image_filechecksum`,`Image`.`image_width` AS `image_width`,`Image`.`image_height` AS `image_height`,`Image`.`mut_deleted` AS `mut_deleted`,`Set`.`set_id` AS `set_id`,`Set`.`set_prefix` AS `set_prefix`,`Set`.`set_name` AS `set_name`,`Set`.`set_containswhat` AS `set_containswhat`,`Model`.`model_id` AS `model_id`,`Model`.`model_firstname` AS `model_firstname`,`Model`.`model_lastname` AS `model_lastname` from ((`Image` left join `Set` on((`Image`.`set_id` = `Set`.`set_id`))) left join `Model` on((`Model`.`model_id` = `Set`.`model_id`))) where ((`Set`.`mut_deleted` = -(1)) and (`Model`.`mut_deleted` = -(1)));

DROP TABLE IF EXISTS `vw_Model`;
CREATE ALGORITHM=UNDEFINED VIEW `vw_Model` AS select `Model`.`model_id` AS `model_id`,`Model`.`model_firstname` AS `model_firstname`,`Model`.`model_lastname` AS `model_lastname`,`Model`.`model_birthdate` AS `model_birthdate`,`Model`.`mut_deleted` AS `mut_deleted`,(select count(`Set`.`model_id`) AS `count(``model_id``)` from `Set` where ((`Set`.`model_id` = `Model`.`model_id`) and (`Set`.`mut_deleted` = -(1)))) AS `model_setcount` from `Model`;

DROP TABLE IF EXISTS `vw_Set`;
CREATE ALGORITHM=UNDEFINED VIEW `vw_Set` AS select `Set`.`set_id` AS `set_id`,`Set`.`set_prefix` AS `set_prefix`,`Set`.`set_name` AS `set_name`,`Set`.`set_containswhat` AS `set_containswhat`,`Set`.`mut_deleted` AS `mut_deleted`,`Model`.`model_id` AS `model_id`,`Model`.`model_firstname` AS `model_firstname`,`Model`.`model_lastname` AS `model_lastname`,(select count(`Image`.`image_id`) AS `COUNT(image_id)` from `Image` where ((`Image`.`set_id` = `Set`.`set_id`) and (`Image`.`mut_deleted` = -(1)))) AS `set_amount_pics_in_db`,(select count(`Video`.`video_id`) AS `COUNT(video_id)` from `Video` where ((`Video`.`set_id` = `Set`.`set_id`) and (`Video`.`mut_deleted` = -(1)))) AS `set_amount_vids_in_db` from (`Set` left join `Model` on((`Set`.`model_id` = `Model`.`model_id`))) where (`Model`.`mut_deleted` = -(1));

DROP TABLE IF EXISTS `vw_Video`;
CREATE ALGORITHM=UNDEFINED VIEW `vw_Video` AS select `Video`.`video_id` AS `video_id`,`Video`.`video_filename` AS `video_filename`,`Video`.`video_fileextension` AS `video_fileextension`,`Video`.`video_filesize` AS `video_filesize`,`Video`.`video_filechecksum` AS `video_filechecksum`,`Video`.`mut_deleted` AS `mut_deleted`,`Set`.`set_id` AS `set_id`,`Set`.`set_prefix` AS `set_prefix`,`Set`.`set_name` AS `set_name`,`Set`.`set_containswhat` AS `set_containswhat`,`Model`.`model_id` AS `model_id`,`Model`.`model_firstname` AS `model_firstname`,`Model`.`model_lastname` AS `model_lastname` from ((`Video` left join `Set` on((`Video`.`set_id` = `Set`.`set_id`))) left join `Model` on((`Model`.`model_id` = `Set`.`model_id`))) where ((`Set`.`mut_deleted` = -(1)) and (`Model`.`mut_deleted` = -(1)));

ALTER TABLE `Date`
  ADD CONSTRAINT `Date_ibfk_1` FOREIGN KEY (`set_id`) REFERENCES `Set` (`set_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `Date_ibfk_2` FOREIGN KEY (`mut_id`) REFERENCES `User` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `Image`
  ADD CONSTRAINT `Image_ibfk_1` FOREIGN KEY (`set_id`) REFERENCES `Set` (`set_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `Image_ibfk_2` FOREIGN KEY (`mut_id`) REFERENCES `User` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `Model`
  ADD CONSTRAINT `Model_ibfk_1` FOREIGN KEY (`mut_id`) REFERENCES `User` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `Set`
  ADD CONSTRAINT `Set_ibfk_1` FOREIGN KEY (`model_id`) REFERENCES `Model` (`model_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `Set_ibfk_2` FOREIGN KEY (`mut_id`) REFERENCES `User` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `Video`
  ADD CONSTRAINT `Video_ibfk_1` FOREIGN KEY (`set_id`) REFERENCES `Set` (`set_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `Video_ibfk_2` FOREIGN KEY (`mut_id`) REFERENCES `User` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
  
ALTER TABLE `CacheImage`
  ADD CONSTRAINT `CacheImage_ibfk_1` FOREIGN KEY (`model_id`) REFERENCES `Model` (`model_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `CacheImage_ibfk_2` FOREIGN KEY (`index_id`) REFERENCES `Model` (`model_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `CacheImage_ibfk_3` FOREIGN KEY (`set_id`)   REFERENCES `Set`   (`set_id`)   ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `CacheImage_ibfk_4` FOREIGN KEY (`image_id`) REFERENCES `Image` (`image_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `CacheImage_ibfk_5` FOREIGN KEY (`video_id`) REFERENCES `Video` (`video_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

COMMIT;

SET AUTOCOMMIT=1;

FjbMNnvUJheiwewUJfheJheuehFJDUHdywgwwgHGfgywug;

$ConfigTemplate = <<<FjbMNnvUJheiwewUJfheJheuehFJDUHdywgwwgHGfgywug
<?php

define('CANDYIMAGEPATH', 			'%1\$s');
define('CANDYVIDEOPATH', 			'%2\$s');
define('CANDYVIDEOTHUMBPATH', 		'%4\$s');

define('DBHOSTNAME',				'%5\$s');
define('DBUSERNAME',				'%6\$s');
define('DBPASSWORD',				'%7\$s');
define('CMDLINE_USERID',			%17\$d);

define('SMTP_FROM_ADDRESS', 		'%8\$s');
define('SMTP_FROM_NAME', 			'%9\$s');
define('SMTP_HOST',		 			'%10\$s');
define('SMTP_USERNAME', 			'%11\$s');
define('SMTP_PASSWORD', 			'%12\$s');
define('SMTP_PORT', 				%13\$d);
define('SMTP_AUTH', 				%14\$s);

\$MailTemplateResetPassword = "Dear %%1\\\$s,\\n".
	"\\n".
	"You have requested the option to reset the password of your CandyDoll DB account.\\n".
	"Click or copy-paste this hyperlink into your browser and follow the onscreen instructions.\\n".
	"\\n".
	"%%2\\\$s\\n".
	"\\n".
	"Best regards and see you soon,\\n".
	"\\n".
	"\\n".
	"CandyDoll DB Admin\\n".
	"%15\$s %16\$s";

?>
FjbMNnvUJheiwewUJfheJheuehFJDUHdywgwwgHGfgywug;

if(array_key_exists('hidAction', $_POST) && isset($_POST['hidAction']) && $_POST['hidAction'] == 'SetupCDDB')
{
	$DBHostName = isset($_POST['txtDBHostName']) && strlen($_POST['txtDBHostName']) > 0 ? (string)$_POST['txtDBHostName'] : null;
	$DBUserName = isset($_POST['txtDBUserName']) && strlen($_POST['txtDBUserName']) > 0 ? (string)$_POST['txtDBUserName'] : null;
	$DBPassword = isset($_POST['txtDBPassword']) && strlen($_POST['txtDBPassword']) >= 0 ? (string)$_POST['txtDBPassword'] : null;
	
	$UserName 		= isset($_POST['txtUserName']) && strlen($_POST['txtUserName']) > 0 ? (string)$_POST['txtUserName'] : null;
	$Password 		= isset($_POST['txtPassword']) && strlen($_POST['txtPassword']) > 0 ? (string)$_POST['txtPassword'] : null;
	$UserFirstName	= isset($_POST['txtFirstName']) && strlen($_POST['txtFirstName']) > 0 ? (string)$_POST['txtFirstName'] : null;
	$UserLastName 	= isset($_POST['txtLastName']) && strlen($_POST['txtLastName']) > 0 ? (string)$_POST['txtLastName'] : null;
	$UserEmail 		= isset($_POST['txtEmail']) && strlen($_POST['txtEmail']) > 0 ? (string)$_POST['txtEmail'] : null;
	
	$CandyImagePath 	= isset($_POST['txtCandyImagePath']) && strlen($_POST['txtCandyImagePath']) > 0 ? (string)$_POST['txtCandyImagePath'] : null;
	$CandyVideoPath 	= isset($_POST['txtCandyVideoPath']) && strlen($_POST['txtCandyVideoPath']) > 0 ? (string)$_POST['txtCandyVideoPath'] : null;
	$CandyVideoThumbPath = isset($_POST['txtCandyVideoThumbPath']) && strlen($_POST['txtCandyVideoThumbPath']) > 0 ? (string)$_POST['txtCandyVideoThumbPath'] : null;
	
	$SmtpFromAddress = isset($_POST['txtSmtpFromAddress']) && strlen($_POST['txtSmtpFromAddress']) > 0 ? (string)$_POST['txtSmtpFromAddress'] : null;
	$SmtpFromName 	= isset($_POST['txtSmtpFromName']) && strlen($_POST['txtSmtpFromName']) > 0 ? (string)$_POST['txtSmtpFromName'] : null;
	$SmtpHostname	= isset($_POST['txtSmtpHostname']) && strlen($_POST['txtSmtpHostname']) > 0 ? (string)$_POST['txtSmtpHostname'] : null;
	$SmtpUsername	= isset($_POST['txtSmtpUsername']) && strlen($_POST['txtSmtpUsername']) > 0 ? (string)$_POST['txtSmtpUsername'] : null;
	$SmtpPassword	= isset($_POST['txtSmtpPassword']) && strlen($_POST['txtSmtpPassword']) > 0 ? (string)$_POST['txtSmtpPassword'] : null;
	$SmtpPort 		= isset($_POST['txtSmtpPort']) && intval($_POST['txtSmtpPort']) > 0 ? intval($_POST['txtSmtpPort']) : 0;
	$SmtpAuth 		= array_key_exists('chkSmtpAuth', $_POST);
	
	if(isset($DBHostName) && isset($DBUserName) && isset($DBPassword))
	{
		if(@mysql_pconnect($DBHostName, $DBUserName, $DBPassword) !== false)
		{
			if(ExecuteQueries($CreateDBSQL) !== false)
			{
				$UserSalt = Utils::GenerateGarbage(20);
				
				@mysql_select_db('candydolldb');
				if(@mysql_query(sprintf(
						$InsertUserSQL,
						mysql_escape_string($UserName),
						mysql_escape_string(Utils::HashString($Password, $UserSalt)),
						mysql_escape_string($UserSalt),
						mysql_escape_string($UserFirstName),
						mysql_escape_string($UserLastName),
						mysql_escape_string($UserEmail)
						)) !== false)
				{
					$NewUserID = mysql_fetch_array(mysql_query('SELECT LAST_INSERT_ID() AS `LastID`;'));
					$NewUserID = intval($NewUserID['LastID']);
					
					$NewConfig = sprintf($ConfigTemplate,
						str_ireplace('\\', '\\\\', $CandyImagePath),
						str_ireplace('\\', '\\\\', $CandyVideoPath),
						null,
						str_ireplace('\\', '\\\\', $CandyVideoThumbPath),
						$DBHostName,
						$DBUserName,
						$DBPassword,
						$SmtpFromAddress,
						$SmtpFromName,
						$SmtpHostname,
						$SmtpUsername,
						$SmtpPassword,
						$SmtpPort,
						$SmtpAuth ? 'true' : 'false',
						$UserFirstName,
						$UserLastName,
						$NewUserID);
					
					if(@file_put_contents('config.php', $NewConfig, LOCK_EX) !== false)
					{
						die('All done! Configuration written to \'config.php\'. Please remove this page from the installation and <a href="login.php">log in</a>.');
					}
					else	
					{ die(sprintf('Something went wrong while writing the new config. Please check permissions and %1$s.', BackToThisPage('try again'))); }
				}
				else 
				{ die(sprintf('Something went wrong while creating the user (\'%2$s\'), please %1$s.', BackToThisPage('try again'), mysql_error())); }	
			}
			else
			{ die(sprintf('Something went wrong while creating the database (\'%2$s\'), please %1$s.', BackToThisPage('try again'), mysql_error())); }	
		}
		else
		{ die(sprintf('Could not connect to the database, please %1$s the database-settings.', BackToThisPage('re-enter'))); }
	}
	else
	{ die(sprintf('Could not connect to the database, please %1$s the database-settings.', BackToThisPage('re-enter'))); }
}
else
{
	$DBHostName = 'localhost';
	$DBUserName = 'username';
	$DBPassword = 'p@ssw0rd';
	
	$UserName = 'your_name_here';
	$Password = 'your_password_here';
	$UserFirstName = 'Firstname';
	$UserLastName = 'Lastname';
	$UserEmail = 'Email-address';

	if(stripos(php_uname('s'), 'WIN') === false)
	{
		$CandyImagePath = '/path/to/candydoll_pics';
		$CandyVideoPath = '/path/to/candydoll_vids';
	}
	else
	{
		$CandyImagePath = 'C:\\Path\\To\\candydoll_pics';
		$CandyVideoPath = 'C:\\Path\\To\\candydoll_vids';
	}

	$CandyVideoThumbPath = 'thumbnails';

	$SmtpFromAddress = 'your@email';
	$SmtpFromName = 'Your Name';
	$SmtpHostname = 'mail.yourdomain.com';
	$SmtpUsername = 'smtp_username';
	$SmtpPassword = 'P@s$w0Rd';
	$SmtpPort = 25;
	$SmtpAuth = true;
}

echo HTMLstuff::HtmlHeader('Setup'); ?>

<h2 class="Hidden">Application Setup</h2>

<div class="CenterForm">

<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post"> 
<fieldset>
 
<legend>Set up your CandyDoll DB:</legend> 
<input type="hidden" id="hidAction" name="hidAction" value="SetupCDDB" />

<h3>Database</h3>

<div class="FormRow">
<label for="txtDBHostName">Hostname: <em>*</em></label>
<input type="text" id="txtDBHostName" name="txtDBHostName" maxlength="100" value="<?php echo $DBHostName;?>" />
</div>

<div class="FormRow">
<label for="txtDBUserName">Username: <em>*</em></label>
<input type="text" id="txtDBUserName" name="txtDBUserName" maxlength="100" value="<?php echo $DBUserName;?>" />
</div>

<div class="FormRow">
<label for="txtDBPassword">Password: <em>*</em></label>
<input type="text" id="txtDBPassword" name="txtDBPassword" maxlength="100" value="<?php echo $DBPassword;?>" />
</div>

<h3>System</h3>

<div class="FormRow">
<label for="txtUserName">Username: <em>*</em></label>
<input type="text" id="txtUserName" name="txtUserName" maxlength="100" value="<?php echo $UserName;?>" />
</div>

<div class="FormRow">
<label for="txtPassword">Password: <em>*</em></label>
<input type="text" id="txtPassword" name="txtPassword" maxlength="100" value="<?php echo $Password;?>" />
</div>

<div class="FormRow">
<label for="txtFirstName">Firstname: <em>*</em></label>
<input type="text" id="txtFirstName" name="txtFirstName" maxlength="100" value="<?php echo $UserFirstName;?>" />
</div>

<div class="FormRow">
<label for="txtLastName">Lastname: <em>*</em></label>
<input type="text" id="txtLastName" name="txtLastName" maxlength="100" value="<?php echo $UserLastName;?>" />
</div>

<div class="FormRow">
<label for="txtEmail">Emailaddress: <em>*</em></label>
<input type="text" id="txtEmail" name="txtEmail" maxlength="255" value="<?php echo $UserEmail;?>" />
</div>

<h3>Candydoll collection</h3>

<div class="FormRow">
<label for="txtCandyImagePath">Image-path:</label>
<input type="text" id="txtCandyImagePath" name="txtCandyImagePath" maxlength="255" value="<?php echo $CandyImagePath;?>" />
</div>

<div class="FormRow">
<label for="txtCandyVideoPath">Video-path:</label>
<input type="text" id="txtCandyVideoPath" name="txtCandyVideoPath" maxlength="255" value="<?php echo $CandyVideoPath;?>" />
</div>

<div class="FormRow">
<label for="txtCandyVideoThumbPath">Thumbnails:</label>
<input type="text" id="txtCandyVideoThumbPath" name="txtCandyVideoThumbPath" maxlength="255" value="<?php echo $CandyVideoThumbPath;?>" />
</div>

<h3>Mailserver (SMTP, optional)</h3>

<div class="FormRow">
<label for="txtSmtpFromAddress">Sender-address: <em>*</em></label>
<input type="text" id="txtSmtpFromAddress" name="txtSmtpFromAddress" maxlength="100" value="<?php echo $SmtpFromAddress;?>" />
</div>

<div class="FormRow">
<label for="txtSmtpFromName">Sender-name: <em>*</em></label>
<input type="text" id="txtSmtpFromName" name="txtSmtpFromName" maxlength="100" value="<?php echo $SmtpFromName;?>" />
</div>

<div class="FormRow">
<label for="txtSmtpHostname">Hostname: <em>*</em></label>
<input type="text" id="txtSmtpHostname" name="txtSmtpHostname" maxlength="100" value="<?php echo $SmtpHostname;?>" />
</div>

<div class="FormRow">
<label for="txtSmtpUsername">Username: <em>*</em></label>
<input type="text" id="txtSmtpUsername" name="txtSmtpUsername" maxlength="100" value="<?php echo $SmtpUsername;?>" />
</div>

<div class="FormRow">
<label for="txtSmtpPassword">Password: <em>*</em></label>
<input type="text" id="txtSmtpPassword" name="txtSmtpPassword" maxlength="100" value="<?php echo $SmtpPassword;?>" />
</div>

<div class="FormRow">
<label for="txtSmtpPassword">Port: <em>*</em></label>
<input type="text" id="txtSmtpPort" name="txtSmtpPort" maxlength="5" value="<?php echo $SmtpPort;?>" />
</div>

<div class="FormRow">
<label for="txtSmtpPassword">SMTP-auth: <em>*</em></label>
<input type="checkbox" id="chkSmtpAuth" name="chkSmtpAuth"<?php echo $SmtpAuth ? ' checked="checked"' : null; ?> />
</div>
   
<input type="submit" id="btnSubmit" name="btnSubmit" value="Setup" /> 
 
</fieldset> 
</form> 
 
</div>

<?php echo HTMLstuff::HtmlFooter(); ?>