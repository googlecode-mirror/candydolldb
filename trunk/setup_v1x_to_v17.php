<?php
/*	This file is part of CandyDollDB.

CandyDollDB is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

CandyDollDB is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with CandyDollDB.  If not, see <http://www.gnu.org/licenses/>.
*/

require('cd.php');

$q = null;
$Exists = false;
$NoError = true;

if(array_key_exists('hidAction', $_POST) && isset($_POST['hidAction']) && $_POST['hidAction'] == 'UpdateCandyDollDB')
{
	/* model_tags column */
	$q = mysql_query("SHOW COLUMNS FROM `Model` LIKE 'model_tags'");
	$Exists = mysql_fetch_assoc($q);  
	
	if($NoError && !$Exists)
	{ $NoError = $db->ExecuteQueries("ALTER TABLE `Model` ADD `model_tags` varchar(200) DEFAULT NULL AFTER `model_birthdate`"); }

	
	/* user_datedisplayoptions column */
	$q = mysql_query("SHOW COLUMNS FROM `User` LIKE 'user_datedisplayopts'");
	$Exists = mysql_fetch_assoc($q);
	
	if($NoError && !$Exists)
	{ $NoError = $db->ExecuteQueries("ALTER TABLE `User` ADD `user_datedisplayopts` int NOT NULL DEFAULT 0 AFTER `user_email`"); }

	
	$UpdateDBSQL = <<<FjbMNnvUJheiwewUJfheJheuehFJDUHdywgwwgHGfgywug
SET AUTOCOMMIT=0;
START TRANSACTION;

CREATE TABLE IF NOT EXISTS `Tag` (
  `tag_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(50) NOT NULL,
  `mut_id` bigint(20) NOT NULL,
  `mut_date` bigint(20) NOT NULL DEFAULT '-1',
  `mut_deleted` bigint(20) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`tag_id`),
  UNIQUE KEY `UNIQ_TAG` (`mut_deleted`,`tag_name`),
  KEY `mut_id` (`mut_id`),
  CONSTRAINT `Tag_ibfk_1` FOREIGN KEY (`mut_id`) REFERENCES `User` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP VIEW IF EXISTS `vw_Image`;
CREATE TABLE IF NOT EXISTS `vw_Image` (
`image_id` bigint(20)
,`image_filename` varchar(100)
,`image_fileextension` varchar(10)
,`image_filesize` bigint(20)
,`image_filechecksum` varchar(32)
,`image_width` int(11)
,`image_height` int(11)
,`image_tags` varchar(200)
,`mut_deleted` bigint(20)
,`set_id` bigint(20)
,`set_prefix` varchar(50)
,`set_name` varchar(100)
,`set_containswhat` tinyint(4)
,`set_tags` varchar(200)
,`model_id` bigint(20)
,`model_firstname` varchar(100)
,`model_lastname` varchar(100)
,`model_tags` varchar(200)
);

DROP VIEW IF EXISTS `vw_Model`;
CREATE TABLE IF NOT EXISTS `vw_Model` (
`model_id` bigint(20)
,`model_firstname` varchar(100)
,`model_lastname` varchar(100)
,`model_birthdate` bigint(20)
,`model_tags` varchar(200)
,`model_remarks` text
,`mut_deleted` bigint(20)
,`model_setcount` bigint(21)
);

DROP VIEW IF EXISTS `vw_Set`;
CREATE TABLE IF NOT EXISTS `vw_Set` (
`set_id` bigint(20)
,`set_prefix` varchar(50)
,`set_name` varchar(100)
,`set_containswhat` tinyint(4)
,`set_tags` varchar(200)
,`mut_deleted` bigint(20)
,`model_id` bigint(20)
,`model_firstname` varchar(100)
,`model_lastname` varchar(100)
,`model_tags` varchar(200)
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
,`video_tags` varchar(200)
,`mut_deleted` bigint(20)
,`set_id` bigint(20)
,`set_prefix` varchar(50)
,`set_name` varchar(100)
,`set_containswhat` tinyint(4)
,`set_tags` varchar(200)
,`model_id` bigint(20)
,`model_firstname` varchar(100)
,`model_lastname` varchar(100)
,`model_tags` varchar(200)
);

DROP TABLE IF EXISTS `vw_Image`;
CREATE ALGORITHM=UNDEFINED VIEW `vw_Image` AS select `Image`.`image_id` AS `image_id`, `Image`.`image_filename` AS `image_filename`, `Image`.`image_fileextension` AS `image_fileextension`, `Image`.`image_filesize` AS `image_filesize`, `Image`.`image_filechecksum` AS `image_filechecksum`, `Image`.`image_width` AS `image_width`, `Image`.`image_height` AS `image_height`, `Image`.`image_tags` AS `image_tags`, `Image`.`mut_deleted` AS `mut_deleted`, `Set`.`set_id` AS `set_id`, `Set`.`set_prefix` AS `set_prefix`, `Set`.`set_name` AS `set_name`, `Set`.`set_containswhat` AS `set_containswhat`, `Set`.`set_tags` AS `set_tags`, `Model`.`model_id` AS `model_id`, `Model`.`model_firstname` AS `model_firstname`, `Model`.`model_lastname` AS `model_lastname`, `Model`.`model_tags` AS `model_tags` from ((`Image` left join `Set` on((`Image`.`set_id` = `Set`.`set_id`))) left join `Model` on((`Model`.`model_id` = `Set`.`model_id`))) where ((`Set`.`mut_deleted` = -(1)) and (`Model`.`mut_deleted` = -(1)));

DROP TABLE IF EXISTS `vw_Model`;
CREATE ALGORITHM=UNDEFINED VIEW `vw_Model` AS select `Model`.`model_id` AS `model_id`,`Model`.`model_firstname` AS `model_firstname`,`Model`.`model_lastname` AS `model_lastname`, `Model`.`model_birthdate` AS `model_birthdate`, `Model`.`model_tags` as `model_tags`, `Model`.`model_remarks` AS `model_remarks`, `Model`.`mut_deleted` AS `mut_deleted`,(select count(`Set`.`model_id`) AS `count(``model_id``)` from `Set` where ((`Set`.`model_id` = `Model`.`model_id`) and (`Set`.`mut_deleted` = -(1)))) AS `model_setcount` from `Model`;

DROP TABLE IF EXISTS `vw_Set`;
CREATE ALGORITHM=UNDEFINED VIEW `vw_Set` AS select `Set`.`set_id` AS `set_id`,`Set`.`set_prefix` AS `set_prefix`,`Set`.`set_name` AS `set_name`,`Set`.`set_containswhat` AS `set_containswhat`, `Set`.`set_tags` as `set_tags`, `Set`.`mut_deleted` AS `mut_deleted`,`Model`.`model_id` AS `model_id`,`Model`.`model_firstname` AS `model_firstname`,`Model`.`model_lastname` AS `model_lastname`,`Model`.`model_tags` as `model_tags`,(select count(`Image`.`image_id`) AS `COUNT(image_id)` from `Image` where ((`Image`.`set_id` = `Set`.`set_id`) and (`Image`.`mut_deleted` = -(1)))) AS `set_amount_pics_in_db`,(select count(`Video`.`video_id`) AS `COUNT(video_id)` from `Video` where ((`Video`.`set_id` = `Set`.`set_id`) and (`Video`.`mut_deleted` = -(1)))) AS `set_amount_vids_in_db` from (`Set` left join `Model` on((`Set`.`model_id` = `Model`.`model_id`))) where (`Model`.`mut_deleted` = -(1));

DROP TABLE IF EXISTS `vw_Video`;
CREATE ALGORITHM=UNDEFINED VIEW `vw_Video` AS select `Video`.`video_id` AS `video_id`,`Video`.`video_filename` AS `video_filename`,`Video`.`video_fileextension` AS `video_fileextension`,`Video`.`video_filesize` AS `video_filesize`,`Video`.`video_filechecksum` AS `video_filechecksum`,`Video`.`video_tags` as `video_tags`,`Video`.`mut_deleted` AS `mut_deleted`,`Set`.`set_id` AS `set_id`,`Set`.`set_prefix` AS `set_prefix`,`Set`.`set_name` AS `set_name`,`Set`.`set_containswhat` AS `set_containswhat`,`Set`.`set_tags` as `set_tags`,`Model`.`model_id` AS `model_id`,`Model`.`model_firstname` AS `model_firstname`,`Model`.`model_lastname` AS `model_lastname`,`Model`.`model_tags` as `model_tags` from ((`Video` left join `Set` on((`Video`.`set_id` = `Set`.`set_id`))) left join `Model` on((`Model`.`model_id` = `Set`.`model_id`))) where ((`Set`.`mut_deleted` = -(1)) and (`Model`.`mut_deleted` = -(1)));
  
COMMIT;
SET AUTOCOMMIT=1;

FjbMNnvUJheiwewUJfheJheuehFJDUHdywgwwgHGfgywug;

	if($NoError && $db->ExecuteQueries($UpdateDBSQL))
	{
		die('The database has been updated, please <a href="login.php">log-in</a>.');
	}
	else
	{
		die(sprintf(
			'Something went wrong while updating, please <a href="%1$s">try again</a>.',
			$_SERVER['REQUEST_URI']
		));
	}
}
else
{
	echo HTMLstuff::HtmlHeader('Setup'); ?>

<h2 class="Hidden">Application Setup</h2>

<div class="CenterForm">

<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post">
<fieldset>

<legend>Update your CandyDoll DB:</legend>
<input type="hidden" id="hidAction" name="hidAction" value="UpdateCandyDollDB" />

<h2 class="Center">Update to v1.7</h2>

<p>Are you sure you want to update your<br />CandyDollDB to v1.7?</p>

<div class="Separator"></div>

<div class="Center">
<input type="submit" id="btnSubmit" name="btnSubmit" value="Yes, please update" />
<input type="button" id="btnCancel" name="btnCancel" value="No thanks" onclick="alert('Then why do you visit this page?'); return false;" />
</div>

</fieldset>
</form>

</div>

<?php
}
echo HTMLstuff::HtmlFooter(); ?>
