<?php

require_once('cd.php');

if(file_exists('config.php'))
{
	$e = new Error(NULL, $lang->g('ErrorSetupAlreadyComplete'));
	Error::AddError($e);
	
	header('location:login.php');
	exit;
}

$DBHostName = NULL;
$DBUserName = NULL;
$DBPassword = NULL;
$DBName = DBNAME;

$UserName = NULL;
$Password = NULL;
$PasswordRepeat = NULL;
$UserFirstName = NULL;
$UserLastName = NULL;
$UserEmail = NULL;

$CandyPath = NULL;
$CandyVideoThumbPath = NULL;

$UseMailServer = FALSE;
$SmtpFromAddress = NULL;
$SmtpFromName = NULL;
$SmtpHostname = NULL;
$SmtpUsername = NULL;
$SmtpPassword = NULL;
$SmtpPort = 0;
$SmtpAuth = FALSE;

$InsertUserSQL = <<<FjbMNnvUJheiwewUJfheJheuehFJDUHdywgwwgHGfgywug
INSERT INTO `User` (
  `user_username`,
  `user_password`,
  `user_salt`,
  `user_firstname`,
  `user_lastname`,
  `user_email`,
  `user_datedisplayopts`,
  `user_imageview`,
  `user_language`,
  `user_rights`,
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
	0,
	'detail',
	'en',
	'%7\$s',
	1,
	UNIX_TIMESTAMP(),
	-1
);
FjbMNnvUJheiwewUJfheJheuehFJDUHdywgwwgHGfgywug;

$CreateDBSQL = <<<FjbMNnvUJheiwewUJfheJheuehFJDUHdywgwwgHGfgywug
START TRANSACTION;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

DROP DATABASE IF EXISTS `%1\$s`;
CREATE DATABASE `%1\$s` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `%1\$s`;

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
  `image_filecrc32` varchar(8) DEFAULT NULL,
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
  `model_remarks` text NULL DEFAULT NULL,
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
  `user_datedisplayopts` int NOT NULL DEFAULT '0',
  `user_gender` tinyint(4) NOT NULL DEFAULT '0',
  `user_imageview` varchar(20) NOT NULL DEFAULT 'detail',
  `user_language` varchar(20) NOT NULL DEFAULT 'en',
  `user_birthdate` bigint(20) NOT NULL DEFAULT '-1',
  `user_lastactive` bigint(20) NOT NULL DEFAULT '-1',
  `user_lastlogin` bigint(20) NOT NULL DEFAULT '-1',
  `user_prelastlogin` bigint(20) NOT NULL DEFAULT '-1',
  `user_rights` text NOT NULL,
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
  `video_filecrc32` varchar(8) DEFAULT NULL,
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
  `index_sequence_number` int(11) NOT NULL DEFAULT '1',
  `index_sequence_total` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`cache_id`, `index_sequence_number`, `index_sequence_total`),
  KEY `model_id` (`model_id`),
  KEY `index_id` (`index_id`),
  KEY `set_id` (`set_id`),
  KEY `image_id` (`image_id`),
  KEY `video_id` (`video_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `Tag`;
CREATE TABLE IF NOT EXISTS `Tag` (
  `tag_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(50) NOT NULL,
  `mut_id` bigint(20) NOT NULL,
  `mut_date` bigint(20) NOT NULL DEFAULT '-1',
  `mut_deleted` bigint(20) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`tag_id`),
  UNIQUE KEY `UNIQ_TAG` (`mut_deleted`,`tag_name`),
  KEY `mut_id` (`mut_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `Tag2All`;
CREATE TABLE IF NOT EXISTS `Tag2All` (
  `tag_id` bigint(20) NOT NULL,
  `model_id` bigint(20) NULL DEFAULT NULL,
  `set_id` bigint(20) NULL DEFAULT NULL,
  `image_id` bigint(20) NULL DEFAULT NULL,
  `video_id` bigint(20) NULL DEFAULT NULL,
  KEY `tag_id` (`tag_id`),
  KEY `model_id` (`model_id`),
  KEY `set_id` (`set_id`),
  KEY `image_id` (`image_id`),
  KEY `video_id` (`video_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

DROP VIEW IF EXISTS `vw_Date`;
CREATE ALGORITHM=UNDEFINED VIEW `vw_Date` AS select `Date`.`date_id` AS `date_id`,`Date`.`date_kind` AS `date_kind`,`Date`.`date_timestamp` AS `date_timestamp`,`Date`.`mut_deleted` AS `mut_deleted`,`Date`.`set_id` AS `set_id`,`Set`.`set_prefix` AS `set_prefix`,`Set`.`set_name` AS `set_name`,`Set`.`set_containswhat` AS `set_containswhat`,`Model`.`model_id` AS `model_id`,`Model`.`model_firstname` AS `model_firstname`,`Model`.`model_lastname` AS `model_lastname` from ((`Date` left join `Set` on((`Set`.`set_id` = `Date`.`set_id`))) left join `Model` on((`Model`.`model_id` = `Set`.`model_id`))) where ((`Set`.`mut_deleted` = -(1)) and (`Model`.`mut_deleted` = -(1)));

DROP VIEW IF EXISTS `vw_Image`;
CREATE ALGORITHM=UNDEFINED VIEW `vw_Image` AS select `Image`.`image_id` AS `image_id`, `Image`.`image_filename` AS `image_filename`, `Image`.`image_fileextension` AS `image_fileextension`, `Image`.`image_filesize` AS `image_filesize`, `Image`.`image_filechecksum` AS `image_filechecksum`, `Image`.`image_filecrc32` AS `image_filecrc32`, `Image`.`image_width` AS `image_width`, `Image`.`image_height` AS `image_height`, `Image`.`mut_deleted` AS `mut_deleted`, `Set`.`set_id` AS `set_id`, `Set`.`set_prefix` AS `set_prefix`, `Set`.`set_name` AS `set_name`, `Set`.`set_containswhat` AS `set_containswhat`, `Model`.`model_id` AS `model_id`, `Model`.`model_firstname` AS `model_firstname`, `Model`.`model_lastname` AS `model_lastname` from ((`Image` left join `Set` on((`Image`.`set_id` = `Set`.`set_id`))) left join `Model` on((`Model`.`model_id` = `Set`.`model_id`))) where ((`Set`.`mut_deleted` = -(1)) and (`Model`.`mut_deleted` = -(1)));

DROP VIEW IF EXISTS `vw_Model`;
CREATE ALGORITHM=UNDEFINED VIEW `vw_Model` AS

	select
		`Model`.`model_id` AS `model_id`,
		`Model`.`model_firstname` AS `model_firstname`,
		`Model`.`model_lastname` AS `model_lastname`,
		`Model`.`model_birthdate` AS `model_birthdate`,
		`Model`.`model_remarks` AS `model_remarks`,
		`Model`.`mut_deleted` AS `mut_deleted`,

		(select
			count(`Set`.`model_id`)
		 from
			`Set`
		where
			((`Set`.`model_id` = `Model`.`model_id`)
			and (`Set`.`mut_deleted` = -(1)))
		) AS `model_setcount`,
		
		(select
			min(`date_timestamp`)
		from
			`vw_Date`
		where
			`vw_Date`.`model_id` = `Model`.`model_id`
			and `vw_Date`.`mut_deleted` = (-1)
		) AS model_firstset,
		
		(select
			max(`date_timestamp`)
		from
			`vw_Date`
		where
			`vw_Date`.`model_id` = `Model`.`model_id`
			and `vw_Date`.`mut_deleted` = (-1)
		) AS model_lastset
		
	from
		`Model`;

DROP VIEW IF EXISTS `vw_Set`;
CREATE ALGORITHM=UNDEFINED VIEW `vw_Set` AS select `Set`.`set_id` AS `set_id`,`Set`.`set_prefix` AS `set_prefix`,`Set`.`set_name` AS `set_name`,`Set`.`set_containswhat` AS `set_containswhat`, `Set`.`mut_deleted` AS `mut_deleted`,`Model`.`model_id` AS `model_id`,`Model`.`model_firstname` AS `model_firstname`,`Model`.`model_lastname` AS `model_lastname`,(select count(`Image`.`image_id`) AS `COUNT(image_id)` from `Image` where ((`Image`.`set_id` = `Set`.`set_id`) and (`Image`.`mut_deleted` = -(1)))) AS `set_amount_pics_in_db`,(select count(`Video`.`video_id`) AS `COUNT(video_id)` from `Video` where ((`Video`.`set_id` = `Set`.`set_id`) and (`Video`.`mut_deleted` = -(1)))) AS `set_amount_vids_in_db` from (`Set` left join `Model` on((`Set`.`model_id` = `Model`.`model_id`))) where (`Model`.`mut_deleted` = -(1));

DROP VIEW IF EXISTS `vw_Video`;
CREATE ALGORITHM=UNDEFINED VIEW `vw_Video` AS select `Video`.`video_id` AS `video_id`,`Video`.`video_filename` AS `video_filename`,`Video`.`video_fileextension` AS `video_fileextension`,`Video`.`video_filesize` AS `video_filesize`,`Video`.`video_filechecksum` AS `video_filechecksum`, `Video`.`video_filecrc32` AS `video_filecrc32`,`Video`.`mut_deleted` AS `mut_deleted`,`Set`.`set_id` AS `set_id`,`Set`.`set_prefix` AS `set_prefix`,`Set`.`set_name` AS `set_name`,`Set`.`set_containswhat` AS `set_containswhat`,`Model`.`model_id` AS `model_id`,`Model`.`model_firstname` AS `model_firstname`,`Model`.`model_lastname` AS `model_lastname` from ((`Video` left join `Set` on((`Video`.`set_id` = `Set`.`set_id`))) left join `Model` on((`Model`.`model_id` = `Set`.`model_id`))) where ((`Set`.`mut_deleted` = -(1)) and (`Model`.`mut_deleted` = -(1)));

DROP VIEW IF EXISTS `vw_Tag2All`;
CREATE ALGORITHM=UNDEFINED VIEW `vw_Tag2All` AS	select `Tag2All`.`tag_id` AS `tag_id`, `Tag`.`tag_name` AS `tag_name`, `Tag2All`.`model_id` AS `model_id`, `Tag2All`.`set_id` AS `set_id`, `Tag2All`.`image_id` AS `image_id`, `Tag2All`.`video_id` AS `video_id` from `Tag2All` join `Tag` on `Tag`.`tag_id` = `Tag2All`.`tag_id`;

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

ALTER TABLE `Tag`
  ADD CONSTRAINT `Tag_ibfk_1` FOREIGN KEY (`mut_id`) REFERENCES `User` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `Tag2All`
  ADD CONSTRAINT `Tag2All_ibfk_1` FOREIGN KEY (`tag_id`)   REFERENCES `Tag`   (`tag_id`)   ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `Tag2All_ibfk_2` FOREIGN KEY (`model_id`) REFERENCES `Model` (`model_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `Tag2All_ibfk_3` FOREIGN KEY (`set_id`)   REFERENCES `Set`   (`set_id`)   ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `Tag2All_ibfk_4` FOREIGN KEY (`image_id`) REFERENCES `Image` (`image_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `Tag2All_ibfk_5` FOREIGN KEY (`video_id`) REFERENCES `Video` (`video_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

COMMIT;

SET AUTOCOMMIT=1;

FjbMNnvUJheiwewUJfheJheuehFJDUHdywgwwgHGfgywug;

$ConfigTemplate = <<<FjbMNnvUJheiwewUJfheJheuehFJDUHdywgwwgHGfgywug
<?php

define('CANDYPATH', 				'%1\$s');
define('CANDYVIDEOTHUMBPATH', 		'%2\$s');

define('DBHOSTNAME',				'%3\$s');
define('DBUSERNAME',				'%4\$s');
define('DBPASSWORD',				'%5\$s');
define('DBNAME',					'%6\$s');
define('CMDLINE_USERID',			%7\$d);

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
	$DBHostName = isset($_POST['txtDBHostName']) && strlen($_POST['txtDBHostName']) > 0 ? (string)$_POST['txtDBHostName']  : NULL;
	$DBUserName = isset($_POST['txtDBUserName']) && strlen($_POST['txtDBUserName']) > 0 ? (string)$_POST['txtDBUserName']  : NULL;
	$DBPassword = isset($_POST['txtDBPassword']) && strlen($_POST['txtDBPassword']) >= 0 ? (string)$_POST['txtDBPassword'] : NULL;
	$DBName 	= isset($_POST['txtDBName']) 	 && strlen($_POST['txtDBName']) > 0 ? 	  (string)$_POST['txtDBName'] 	   : NULL;

	$UserName 		= isset($_POST['txtUserName']) && strlen($_POST['txtUserName']) > 0 ? (string)$_POST['txtUserName'] : NULL;
	$Password 		= isset($_POST['txtPassword']) && strlen($_POST['txtPassword']) > 0 ? (string)$_POST['txtPassword'] : NULL;
	$PasswordRepeat	= isset($_POST['txtRepeatPassword']) && strlen($_POST['txtRepeatPassword']) > 0 ? (string)$_POST['txtRepeatPassword'] : NULL;
	$UserFirstName	= isset($_POST['txtFirstName']) && strlen($_POST['txtFirstName']) > 0 ? (string)$_POST['txtFirstName'] : NULL;
	$UserLastName 	= isset($_POST['txtLastName']) && strlen($_POST['txtLastName']) > 0 ? (string)$_POST['txtLastName'] : NULL;
	$UserEmail 		= isset($_POST['txtEmail']) && strlen($_POST['txtEmail']) > 0 ? (string)$_POST['txtEmail'] : NULL;

	$CandyPath 	= isset($_POST['txtCandyPath']) && strlen($_POST['txtCandyPath']) > 0 ? (string)$_POST['txtCandyPath'] : NULL;
	$CandyVideoThumbPath = isset($_POST['txtCandyVideoThumbPath']) && strlen($_POST['txtCandyVideoThumbPath']) > 0 ? (string)$_POST['txtCandyVideoThumbPath'] : NULL;

	$UseMailServer 	= array_key_exists('chkUseMailServer', $_POST);
	$SmtpFromAddress = isset($_POST['txtSmtpFromAddress']) && strlen($_POST['txtSmtpFromAddress']) > 0 ? (string)$_POST['txtSmtpFromAddress'] : NULL;
	$SmtpFromName 	= isset($_POST['txtSmtpFromName']) && strlen($_POST['txtSmtpFromName']) > 0 ? (string)$_POST['txtSmtpFromName'] : NULL;
	$SmtpHostname	= isset($_POST['txtSmtpHostname']) && strlen($_POST['txtSmtpHostname']) > 0 ? (string)$_POST['txtSmtpHostname'] : NULL;
	$SmtpUsername	= isset($_POST['txtSmtpUsername']) && strlen($_POST['txtSmtpUsername']) > 0 ? (string)$_POST['txtSmtpUsername'] : NULL;
	$SmtpPassword	= isset($_POST['txtSmtpPassword']) && strlen($_POST['txtSmtpPassword']) > 0 ? (string)$_POST['txtSmtpPassword'] : NULL;
	$SmtpPort 		= isset($_POST['txtSmtpPort']) && intval($_POST['txtSmtpPort']) > 0 ? intval($_POST['txtSmtpPort']) : 0;
	$SmtpAuth 		= array_key_exists('chkSmtpAuth', $_POST);

	$PasswordOK = ($_POST['txtRepeatPassword'] == $_POST['txtPassword']);
	$EmailOK = Utils::ValidateEmail($UserEmail);
	$DBsettingsSet = isset($DBHostName) && isset($DBUserName) && isset($DBPassword); 
	
	if($PasswordOK && $EmailOK && $DBsettingsSet)
	{
		/* @var $dbi DBi */
		if(@($dbi = new DBi($DBHostName, $DBUserName, $DBPassword, 'mysql')))
		{
			$DBConnectOK = ($dbi->connect_errno == 0);
			if($DBConnectOK)
			{
				if($dbi->ExecuteMulti(sprintf($CreateDBSQL, $dbi->real_escape_string($DBName))))
				{
					$UserSalt = Utils::GenerateGarbage(20);
	
					if($dbi->query(sprintf(
						$InsertUserSQL,
						$dbi->real_escape_string($UserName),
						$dbi->real_escape_string(Utils::HashString($Password, $UserSalt)),
						$dbi->real_escape_string($UserSalt),
						$dbi->real_escape_string($UserFirstName),
						$dbi->real_escape_string($UserLastName),
						$dbi->real_escape_string($UserEmail),
						$dbi->real_escape_string(serialize(Rights::getTotalRights()))
					)))
					{
						$NewUserID = $dbi->insert_id;
	
						$NewConfig = sprintf($ConfigTemplate,
							str_ireplace('\\', '\\\\', $CandyPath),
							str_ireplace('\\', '\\\\', $CandyVideoThumbPath),
							$DBHostName,
							$DBUserName,
							$DBPassword,
							$DBName,
							$NewUserID,
							$SmtpFromAddress,
							$SmtpFromName,
							$SmtpHostname,
							$SmtpUsername,
							$SmtpPassword,
							$SmtpPort,
							$SmtpAuth ? 'TRUE' : 'FALSE',
							$UserFirstName,
							$UserLastName);
	
						if(@file_put_contents('config.php', $NewConfig, LOCK_EX) !== FALSE)
						{
							if(is_dir('cache') || mkdir('cache', 0700, TRUE))
							{
								$i = new Info($lang->g('MessageAllDoneConfigWritten'));
								Info::AddInfo($i);
								
								header('location:login.php');
								exit;
							}
							else
							{
								$e = new Error(NULL, $lang->g('ErrorSetupCreatingCacheDir'));
								Error::AddError($e);
							}
						}
						else
						{
							$e = new Error(NULL, $lang->g('ErrorSetupWritingConfig'));
							Error::AddError($e);
						}
					}
					else
					{
						$e = new Error($dbi->error, $lang->g('ErrorSetupCreatingUser'));
						Error::AddError($e);
					}
				}
				else
				{
					$e = new Error($dbi->error, $lang->g('ErrorSetupCreatingDatabase'));
					Error::AddError($e);
				}
			}
			else
			{
				$e = new Error($dbi->connect_errno, $dbi->connect_error);
				Error::AddError($e);
			}
		}
		else
		{
			$e = new Error(NULL, $lang->g('ErrorSetupConnectDatabase'));
			Error::AddError($e);
		}
	}
	else 
	{
		if(!$DBsettingsSet)
		{
			$e = new Error(NULL, $lang->g('ErrorSetupConnectDatabase'));
			Error::AddError($e);
		}
		
		if(!$EmailOK)
		{
			$e = new SyntaxError(SYNTAX_ERR_EMAILADDRESS);
			Error::AddError($e);
		}
		
		if(!$PasswordOK)
		{
			$e = new LoginError(LOGIN_ERR_PASSWORDSNOTIDENTICAL);
			Error::AddError($e);
		}
	}
}
else
{
	$DBHostName = 'localhost';
	$DBUserName = $lang->g('LabelUsername');
	$DBPassword = $lang->g('LabelPasswordGarbage');

	$UserName = $lang->g('LabelUsername');
	$Password = $lang->g('LabelPassword');
	$PasswordRepeat = $lang->g('LabelPassword');
	$UserFirstName = $lang->g('LabelFirstname');
	$UserLastName = $lang->g('LabelLastname');
	$UserEmail = $lang->g('LabelEmailAddress');

	if(stripos(php_uname('s'), 'WIN') === FALSE)
	{
		$CandyPath = $lang->g('LabelPathToCandyDollLinux');
	}
	else
	{
		$CandyPath = $lang->g('LabelPathToCandyDollWin');
	}

	$CandyVideoThumbPath = 'thumbnails';

	$SmtpFromAddress = 'your@email';
	$SmtpFromName = 'Your Name';
	$SmtpHostname = 'mail.yourdomain.com';
	$SmtpUsername = 'smtp_username';
	$SmtpPassword = 'P@s$w0Rd';
	$SmtpPort = 25;
	$SmtpAuth = TRUE;
}

echo HTMLstuff::HtmlHeader($lang->g('LabelSetup'))?>

<h2 class="Hidden"><?php echo $lang->g('LabelSetup')?></h2>

<div class="CenterForm">

<form action="<?php echo htmlentities($_SERVER['REQUEST_URI'])?>" method="post">
<fieldset>

<input type="hidden" id="hidAction" name="hidAction" value="SetupCDDB" />

<h3><?php echo $lang->g('LabelDatabase')?></h3>

<div class="FormRow">
<label for="txtDBHostName"><?php echo $lang->g('LabelHostname')?>: <em>*</em></label>
<input type="text" id="txtDBHostName" name="txtDBHostName" maxlength="100" value="<?php echo $DBHostName?>" />
</div>

<div class="FormRow">
<label for="txtDBUserName"><?php echo $lang->g('LabelUsername')?>: <em>*</em></label>
<input type="text" id="txtDBUserName" name="txtDBUserName" maxlength="100" value="<?php echo $DBUserName?>" />
</div>

<div class="FormRow">
<label for="txtDBPassword"><?php echo $lang->g('LabelPassword')?>: <em>*</em></label>
<input type="text" id="txtDBPassword" name="txtDBPassword" maxlength="100" value="<?php echo $DBPassword?>" />
</div>

<div class="FormRow">
<label for="txtDBName"><?php echo $lang->g('LabelDatabaseName')?>: <em>*</em></label>
<input type="text" id="txtDBName" name="txtDBName" maxlength="100" value="<?php echo $DBName?>" />
</div>

<h3>Candydoll <?php echo $lang->g('LabelCollection')?></h3>

<div class="FormRow">
<label for="txtCandyPath"><?php echo $lang->g('LabelPathOnDisk')?>:</label>
<input type="text" id="txtCandyPath" name="txtCandyPath" maxlength="255" value="<?php echo $CandyPath?>" />
</div>

<div class="FormRow">
<label for="txtCandyVideoThumbPath"><?php echo $lang->g('LabelThumbnails')?>:</label>
<input type="text" id="txtCandyVideoThumbPath" name="txtCandyVideoThumbPath" maxlength="255" value="<?php echo $CandyVideoThumbPath?>" />
</div>

<h3><?php echo $lang->g('LabelSystem')?></h3>

<div class="FormRow">
<label for="txtUserName"><?php echo $lang->g('LabelUsername')?>: <em>*</em></label>
<input type="text" id="txtUserName" name="txtUserName" maxlength="100" value="<?php echo $UserName?>" />
</div>

<div class="FormRow">
<label for="txtPassword"><?php echo $lang->g('LabelPassword')?>: <em>*</em></label>
<input type="password" id="txtPassword" name="txtPassword" maxlength="100" value="<?php echo $Password?>" />
</div>

<div class="FormRow">
<label for="txtRepeatPassword"><?php echo $lang->g('LabelRepeatPassword')?>: <em>*</em></label>
<input type="password" id="txtRepeatPassword" name="txtRepeatPassword" maxlength="100" value="<?php echo $PasswordRepeat?>" />
</div>

<div class="FormRow">
<label for="txtFirstName"><?php echo $lang->g('LabelFirstname')?>: <em>*</em></label>
<input type="text" id="txtFirstName" name="txtFirstName" maxlength="100" value="<?php echo $UserFirstName?>" />
</div>

<div class="FormRow">
<label for="txtLastName"><?php echo $lang->g('LabelLastname')?>: <em>*</em></label>
<input type="text" id="txtLastName" name="txtLastName" maxlength="100" value="<?php echo $UserLastName?>" />
</div>

<div class="FormRow">
<label for="txtEmail"><?php echo $lang->g('LabelEmailAddress')?>: <em>*</em></label>
<input type="text" id="txtEmail" name="txtEmail" maxlength="255" value="<?php echo $UserEmail?>" />
</div>

<div class="Separator"></div>

<div class="FormRow">
<label for="chkUseMailServer"><?php echo $lang->g('LabelUseMailServer')?>:</label>
<input type="checkbox" id="chkUseMailServer" name="chkUseMailServer"<?php echo $UseMailServer ? ' checked="checked"' : NULL?> onclick="$('#MailSettings').toggleClass('Hidden');" />
</div>

<div id="MailSettings" class="Hidden">

<h3><?php echo $lang->g('LabelMailServer')?></h3>

<div class="FormRow">
<label for="txtSmtpFromAddress"><?php echo $lang->g('LabelSenderAddress')?>: <em>*</em></label>
<input type="text" id="txtSmtpFromAddress" name="txtSmtpFromAddress" maxlength="100" value="<?php echo $SmtpFromAddress?>" />
</div>

<div class="FormRow">
<label for="txtSmtpFromName"><?php echo $lang->g('LabelSenderName')?>: <em>*</em></label>
<input type="text" id="txtSmtpFromName" name="txtSmtpFromName" maxlength="100" value="<?php echo $SmtpFromName?>" />
</div>

<div class="FormRow">
<label for="txtSmtpHostname"><?php echo $lang->g('LabelHostname')?>: <em>*</em></label>
<input type="text" id="txtSmtpHostname" name="txtSmtpHostname" maxlength="100" value="<?php echo $SmtpHostname?>" />
</div>

<div class="FormRow">
<label for="txtSmtpUsername"><?php echo $lang->g('LabelUsername')?>: <em>*</em></label>
<input type="text" id="txtSmtpUsername" name="txtSmtpUsername" maxlength="100" value="<?php echo $SmtpUsername?>" />
</div>

<div class="FormRow">
<label for="txtSmtpPassword"><?php echo $lang->g('LabelPassword')?>: <em>*</em></label>
<input type="text" id="txtSmtpPassword" name="txtSmtpPassword" maxlength="100" value="<?php echo $SmtpPassword?>" />
</div>

<div class="FormRow">
<label for="txtSmtpPort"><?php echo $lang->g('LabelPort')?>: <em>*</em></label>
<input type="text" id="txtSmtpPort" name="txtSmtpPort" maxlength="5" value="<?php echo $SmtpPort?>" />
</div>

<div class="FormRow">
<label for="chkSmtpAuth"><?php echo $lang->g('LabelSMTPAuth')?>: <em>*</em></label>
<input type="checkbox" id="chkSmtpAuth" name="chkSmtpAuth"<?php echo HTMLstuff::CheckedStr($SmtpAuth)?> />
</div>

</div>

<div class="Separator"></div>

<input type="submit" id="btnSubmit" name="btnSubmit" value="<?php echo $lang->g('ButtonSetup')?>" />

</fieldset>
</form>

</div>

<?php
echo HTMLstuff::HtmlFooter();
?>