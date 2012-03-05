<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();

$q = null;

if(array_key_exists('hidAction', $_POST) && $_POST['hidAction'] == 'Search')
{
	$q = $_POST['q'];
}

echo HTMLstuff::HtmlHeader('Tag search', $CurrentUser);

?>

<h2><?php echo sprintf('<a href="index.php">Home</a> - Tag search'); ?></h2>

<form action="<?php echo htmlentities($_SERVER['REQUEST_URI']);?>" method="post">
<fieldset><legend>Please fill in these fields:</legend>

<input type="hidden" id="hidAction" name="hidAction" value="Search" />

<input type="text" id="q" name="q" value="<?php echo $q; ?>" />
<input type="submit" class="FormButton" value="Search" />

<div class="Separator"></div>

<?php echo HTMLstuff::Button('index.php'); ?>

</fieldset>
</form>

<?php
echo HTMLstuff::HtmlFooter($CurrentUser);
?>

?>