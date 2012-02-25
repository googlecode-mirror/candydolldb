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

$UpdateDBSQL = <<<FjbMNnvUJheiwewUJfheJheuehFJDUHdywgwwgHGfgywug
SET AUTOCOMMIT=0;
START TRANSACTION;

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

ALTER TABLE Model ADD model_remarks TEXT NULL DEFAULT NULL AFTER model_birthdate;

DROP VIEW IF EXISTS `vw_Model`;

CREATE ALGORITHM=UNDEFINED VIEW `vw_Model` AS select `Model`.`model_id` AS `model_id`,`Model`.`model_firstname` AS `model_firstname`,`Model`.`model_lastname` AS `model_lastname`,`Model`.`model_birthdate` AS `model_birthdate`, `Model`.`model_remarks` AS `model_remarks`, `Model`.`mut_deleted` AS `mut_deleted`,(select count(`Set`.`model_id`) AS `count(``model_id``)` from `Set` where ((`Set`.`model_id` = `Model`.`model_id`) and (`Set`.`mut_deleted` = -(1)))) AS `model_setcount` from `Model`;

COMMIT;
SET AUTOCOMMIT=1;

FjbMNnvUJheiwewUJfheJheuehFJDUHdywgwwgHGfgywug;

if(array_key_exists('hidAction', $_POST) && isset($_POST['hidAction']) && $_POST['hidAction'] == 'UpdateCandyDollDB')
{
	/* @var $db DB */
	if($db->ExecuteQueries($UpdateDBSQL))
	{
		die(
			'The database has been updated, please <a href="login.php">log-in</a>.'
		);
	}
}

echo HTMLstuff::HtmlHeader('Setup'); ?>

<h2 class="Hidden">Application Setup</h2>

<div class="CenterForm">

<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post"> 
<fieldset>
 
<legend>Update your CandyDoll DB:</legend> 
<input type="hidden" id="hidAction" name="hidAction" value="UpdateCandyDollDB" />

<h2 class="Center">Update to v1.6</h2>

<p>Are you sure you want to update your<br />CandyDollDB to v1.6?</p>

<div class="Separator"></div>

<div class="Center">
<input type="submit" id="btnSubmit" name="btnSubmit" value="Yes, please update" />
<input type="button" id="btnCancel" name="btnCancel" value="No thanks" onclick="alert('Then why do you visit this page?'); return false;" />
</div>
 
</fieldset> 
</form> 
 
</div>

<?php echo HTMLstuff::HtmlFooter(); ?>