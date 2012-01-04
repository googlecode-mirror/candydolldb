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

COMMIT;
SET AUTOCOMMIT=1;

FjbMNnvUJheiwewUJfheJheuehFJDUHdywgwwgHGfgywug;

if(array_key_exists('hidAction', $_POST) && isset($_POST['hidAction']) && $_POST['hidAction'] == 'UpdateCandyDollDB13-14')
{
	/* @var $db DB */
	if($db->ExecuteQueries($UpdateDBSQL))
	{
		die(
			'The database has been updated, please <a href="login.php">log-in</a>.'
		);
	}
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 

<head> 
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
<meta name="language" content="en-US" /> 

<link rel="stylesheet" type="text/css" href="style.css" title="CandyDoll DB" /> 
<link rel="shortcut icon" href="favicon.ico" /> 
<link rel="icon" href="favicon.ico" /> 
		
<script type="text/javascript" src="http://code.jquery.com/jquery.min.js"></script> 
<script type="text/javascript" src="js/fwiep.js"></script> 
<title>CandyDoll DB :: Update</title> 
</head> 
 
<body> 
 
<div id="Container"> 
<div id="Header"> 
 
<h1>CandyDoll DB :: Update</h1> 
<p>by <a href="http://www.fwiep.nl/" rel="external" title="FWieP">FWieP</a></p> 
	
</div> 
 
<div class="CenterForm"> 

<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post"> 
<fieldset>
 
<legend>Update your CandyDoll DB:</legend> 
<input type="hidden" id="hidAction" name="hidAction" value="UpdateCandyDollDB13-14" />

<h2 class="Center">Update v1.3 to v1.4</h2>

<p>Are you sure you want to update your<br />CandyDollDB from v1.3 to v1.4?</p>

<div class="Separator"></div>

<div class="Center">
<input type="submit" id="btnSubmit" name="btnSubmit" value="Yes, please update" />
<input type="button" id="btnCancel" name="btnCancel" value="No thanks" onclick="alert('Then why do you visit this page?'); return false;" />
</div> 
 
</fieldset> 
</form> 
 
</div> 
 
</div> 
 
</body> 
</html>