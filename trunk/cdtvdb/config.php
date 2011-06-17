<?php

define('CANDYIMAGEPATH', 	'/terrafwiep1/lost+gone/candy');
define('CANDYVIDEOPATH', 	'/terrafwiep1/lost+gone/candy_vid');
define('CANDYINDEXPATH', 	'0_index');
define('CANDYVIDEOTHUMBPATH',	'0_thumbs');

define('DBHOSTNAME',		'localhost');
define('DBUSERNAME',		'fwiep');
define('DBPASSWORD',		'GomFbprU1z');
define('CMDLINE_USERID',	3);

define('SMTP_FROM_ADDRESS', 	'info@fwiep.nl');
define('SMTP_FROM_NAME', 	'CandyDoll DB Admin');
define('SMTP_HOST',		'smtp.fwiep.nl');
define('SMTP_USERNAME', 	'info@fwiep.nl');
define('SMTP_PASSWORD', 	'GtKQoA32az');
define('SMTP_PORT', 		25025);
define('SMTP_AUTH', 		true);

$MailTemplateResetPassword = "Dear %1\$s,\n".
	"\n".
	"You have requested the option to reset the password of your CandyDoll DB account.\n".
	"Click or copy-paste this hyperlink into your browser and follow the onscreen instructions.\n".
	"\n".
	"%2\$s\n".
	"\n".
	"Best regards and see you soon,\n".
	"\n".
	"\n".
	"CandyDoll DB Admin\n".
	"Frans-Willem Post";

?>
