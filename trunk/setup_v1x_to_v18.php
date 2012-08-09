<?php
/* This file is part of CandyDollDB.

CandyDollDB is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

CandyDollDB is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with CandyDollDB. If not, see <http://www.gnu.org/licenses/>.
*/

require('cd.php');

$Exists = FALSE;
$NoError = TRUE;

if(array_key_exists('hidAction', $_POST) && isset($_POST['hidAction']) && $_POST['hidAction'] == 'UpdateCandyDollDB')
{
	/* user_datedisplayoptions column */
	$Exists = $dbi->ColumnExists('User', 'user_datedisplayopts');

	if($NoError && !$Exists)
	{ $NoError = $dbi->ExecuteMulti("ALTER TABLE `User` ADD `user_datedisplayopts` int NOT NULL DEFAULT 0 AFTER `user_email`;"); }

	/* user_imageview column */
	$Exists = $dbi->ColumnExists('User', 'user_imageview');

	if($NoError && !$Exists)
	{ $NoError = $dbi->ExecuteMulti("ALTER TABLE `User` ADD `user_imageview` varchar(20) NOT NULL DEFAULT 'detail' AFTER `user_datedisplayopts`;"); }
	
	/* user_language column */
	$Exists = $dbi->ColumnExists('User', 'user_language');

	if($NoError && !$Exists)
	{ $NoError = $dbi->ExecuteMulti("ALTER TABLE `User` ADD `user_language` varchar(20) NOT NULL DEFAULT 'en' AFTER `user_imageview`;"); }
	
	/* user_rights column */
	if($NoError)
	{
		if($dbi->ColumnExists('User', 'user_rights'))
		{ $NoError = $dbi->ExecuteMulti("ALTER TABLE `User` CHANGE `user_rights` `user_rights` text NOT NULL;"); }
		else 
		{ $NoError = $dbi->ExecuteMulti("ALTER TABLE `User` ADD `user_rights` text NOT NULL AFTER `user_prelastlogin`;"); }
	}
	
	/* image_filecrc32 column */
	$Exists = $dbi->ColumnExists('Image', 'image_filecrc32');
	
	if($NoError && !$Exists)
	{ $NoError = $dbi->ExecuteMulti("ALTER TABLE `Image` ADD `image_filecrc32` varchar(8) NULL AFTER `image_filechecksum`;"); }
	
	/* video_filecrc32 column */
	$Exists = $dbi->ColumnExists('Video', 'video_filecrc32');
	
	if($NoError && !$Exists)
	{ $NoError = $dbi->ExecuteMulti("ALTER TABLE `Video` ADD `video_filecrc32` varchar(8) NULL AFTER `video_filechecksum`;"); }

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
  KEY `video_id` (`video_id`),
  
  CONSTRAINT `Tag2All_ibfk_1` FOREIGN KEY (`tag_id`)   REFERENCES `Tag`   (`tag_id`)   ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `Tag2All_ibfk_2` FOREIGN KEY (`model_id`) REFERENCES `Model` (`model_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `Tag2All_ibfk_3` FOREIGN KEY (`set_id`)   REFERENCES `Set`   (`set_id`)   ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `Tag2All_ibfk_4` FOREIGN KEY (`image_id`) REFERENCES `Image` (`image_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `Tag2All_ibfk_5` FOREIGN KEY (`video_id`) REFERENCES `Video` (`video_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
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

ALTER TABLE `CacheImage`
  ADD CONSTRAINT `CacheImage_ibfk_1` FOREIGN KEY (`model_id`) REFERENCES `Model` (`model_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `CacheImage_ibfk_2` FOREIGN KEY (`index_id`) REFERENCES `Model` (`model_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `CacheImage_ibfk_3` FOREIGN KEY (`set_id`)   REFERENCES `Set`   (`set_id`)   ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `CacheImage_ibfk_4` FOREIGN KEY (`image_id`) REFERENCES `Image` (`image_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `CacheImage_ibfk_5` FOREIGN KEY (`video_id`) REFERENCES `Video` (`video_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

DROP VIEW IF EXISTS `vw_Image`;
CREATE ALGORITHM=UNDEFINED VIEW `vw_Image` AS select `Image`.`image_id` AS `image_id`, `Image`.`image_filename` AS `image_filename`, `Image`.`image_fileextension` AS `image_fileextension`, `Image`.`image_filesize` AS `image_filesize`, `Image`.`image_filechecksum` AS `image_filechecksum`, `Image`.`image_filecrc32` AS `image_filecrc32`, `Image`.`image_width` AS `image_width`, `Image`.`image_height` AS `image_height`, `Image`.`mut_deleted` AS `mut_deleted`, `Set`.`set_id` AS `set_id`, `Set`.`set_prefix` AS `set_prefix`, `Set`.`set_name` AS `set_name`, `Set`.`set_containswhat` AS `set_containswhat`, `Model`.`model_id` AS `model_id`, `Model`.`model_firstname` AS `model_firstname`, `Model`.`model_lastname` AS `model_lastname` from ((`Image` left join `Set` on((`Image`.`set_id` = `Set`.`set_id`))) left join `Model` on((`Model`.`model_id` = `Set`.`model_id`))) where ((`Set`.`mut_deleted` = -(1)) and (`Model`.`mut_deleted` = -(1)));

DROP VIEW IF EXISTS `vw_Video`;
CREATE ALGORITHM=UNDEFINED VIEW `vw_Video` AS select `Video`.`video_id` AS `video_id`,`Video`.`video_filename` AS `video_filename`,`Video`.`video_fileextension` AS `video_fileextension`,`Video`.`video_filesize` AS `video_filesize`,`Video`.`video_filechecksum` AS `video_filechecksum`, `Video`.`video_filecrc32` AS `video_filecrc32`,`Video`.`mut_deleted` AS `mut_deleted`,`Set`.`set_id` AS `set_id`,`Set`.`set_prefix` AS `set_prefix`,`Set`.`set_name` AS `set_name`,`Set`.`set_containswhat` AS `set_containswhat`,`Model`.`model_id` AS `model_id`,`Model`.`model_firstname` AS `model_firstname`,`Model`.`model_lastname` AS `model_lastname` from ((`Video` left join `Set` on((`Video`.`set_id` = `Set`.`set_id`))) left join `Model` on((`Model`.`model_id` = `Set`.`model_id`))) where ((`Set`.`mut_deleted` = -(1)) and (`Model`.`mut_deleted` = -(1)));
  
DROP VIEW IF EXISTS `vw_Tag2All`;
CREATE ALGORITHM=UNDEFINED VIEW `vw_Tag2All` AS	select `Tag2All`.`tag_id` AS `tag_id`, `Tag`.`tag_name` AS `tag_name`, `Tag2All`.`model_id` AS `model_id`, `Tag2All`.`set_id` AS `set_id`, `Tag2All`.`image_id` AS `image_id`, `Tag2All`.`video_id` AS `video_id` from `Tag2All` join `Tag` on `Tag`.`tag_id` = `Tag2All`.`tag_id`;
  
COMMIT;
SET AUTOCOMMIT=1;

FjbMNnvUJheiwewUJfheJheuehFJDUHdywgwwgHGfgywug;

	if($NoError && $dbi->ExecuteMulti($UpdateDBSQL))
	{
		/* Rename all cached images on disk to include prefix */
		$CacheImagesInDB = CacheImage::GetCacheImages();
		
		/* @var $ci CacheImage */
		foreach ($CacheImagesInDB as $ci)
		{
			if(file_exists($ci->getFilenameOnDisk(TRUE)))
			{ rename($ci->getFilenameOnDisk(TRUE), $ci->getFilenameOnDisk(FALSE)); }
		}
		
		/* Give the admin-user full rights */
		$admUser = User::GetUsers(new UserSearchParameters(CMDLINE_USERID));
		
		/* @var $admUser User */
		if($admUser)
		{
			$admUser = $admUser[0];
			$admUser->setRights(Rights::getTotalRights());
			User::Update($admUser, $admUser);
		}
		
		if(is_dir('cache') || mkdir('cache', 0700, TRUE))
		{
			$i = new Info($lang->g('MessageDataseUpdated'));
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
		$e = new Error(NULL, $lang->g('ErrorUpdateTryAgain'));
		Error::AddError($e);
	}
}

echo HTMLstuff::HtmlHeader($lang->g('LabelSetup'))?>

<h2 class="Hidden"><?php echo $lang->g('LabelSetup')?></h2>

<div class="CenterForm">

<form action="<?php echo htmlentities($_SERVER['REQUEST_URI'])?>" method="post">
<fieldset>

<input type="hidden" id="hidAction" name="hidAction" value="UpdateCandyDollDB" />

<h2 class="Center"><?php echo sprintf($lang->g('LabelUpdateToVersionX'), CANDYDOLLDB_VERSION)?></h2>

<?php echo sprintf($lang->g('MessageSureUpdateToX'), CANDYDOLLDB_VERSION)?>

<div class="Separator"></div>

<div class="Center">
<input type="submit" id="btnSubmit" name="btnSubmit" value="<?php echo $lang->g('ButtonYesPleaseUpdate' )?>" />
<input type="button" id="btnCancel" name="btnCancel" value="<?php echo $lang->g('ButtonNoThanks' )?>" onclick="return false;" />
</div>

</fieldset>
</form>

</div>

<?php
echo HTMLstuff::HtmlFooter();
?>