<?php


require('cd.php');

if(array_key_exists('hidAction', $_POST) && isset($_POST['hidAction']) && $_POST['hidAction'] == 'UpdateCandyDollDB')
{
    $exists = false;
    $columns = mysql_query("SHOW COLUMNS FROM User LIKE 'user_datedisplayopts'");
    while($c = mysql_fetch_assoc($columns)){
        if($c['Field'] == $columns){
            $exists = true;
            break;
        }
    }
    if(!$exists)
    {
        mysql_query("ALTER TABLE user ADD user_datedisplayopts INT NOT NULL DEFAULT 0 AFTER user_email");
        $exists = true;
    }


	/* @var $db DB*/
	if($exists == true)
	{
		die(
			'The database has been updated, please <a href="login.php">log-in</a>.'
		);
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
echo HTMLstuff::HtmlFooter();
?>