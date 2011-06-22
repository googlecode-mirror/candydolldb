<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();

$UserRows = '';
$UserCount = 0;

$Users = User::GetUsers();

if($Users)
{
	/* @var $User User */
	foreach($Users as $User)
	{
		$UserCount++;

		$UserRows .= sprintf(
		"\n<tr class=\"Row%10\$d\">".
    		"<td><a href=\"user_view.php?user_id=%1\$d\">%2\$s</a></td>".
	        "<td><a href=\"user_view.php?user_id=%1\$d\">%3\$s</a></td>".
        	"<td class=\"Center\"><a href=\"user_view.php?user_id=%1\$d\">%4\$s</a></td>".
        	"<td class=\"Center\"%6\$s><a href=\"user_view.php?user_id=%1\$d\">%5\$s</a></td>".
			"<td class=\"Center\"><a href=\"user_view.php?user_id=%1\$d\">%7\$s</a></td>".
			"<td class=\"Center\"><a href=\"user_view.php?user_id=%1\$d\">%8\$s</a></td>".
			"<td class=\"Center\"><a href=\"user_view.php?user_id=%1\$d&amp;cmd=%9\$s\" title=\"Delete user\"><img src=\"images/button_delete.png\" width=\"16\" height=\"16\" alt=\"Delete\" /></a></td>".
        "</tr>",
		$User->getID(),
		htmlentities($User->getUserName()),
		htmlentities($User->GetFullName()),
		($User->getGender() == GENDER_FEMALE ? 'f' : ($User->getGender() == GENDER_MALE ? 'm' : '?')),
		$User->getBirthdate() > 0 ? date('j-m-Y', $User->getBirthdate()) : '&nbsp;',
		$User->getBirthdate() > 0 ? ' title="'.date('l', $User->getBirthdate()).'"' : null,
		$User->getLastActive() > 0 ? date('j-n-Y G:i', $User->getLastActive()) : '&nbsp;',
		$User->getLastLogin() > 0 ? date('j-n-Y G:i', $User->getLastLogin()) : '&nbsp;',
		COMMAND_DELETE,
		$UserCount % 2 == 0 ? 2 : 1
		);
	}
	unset($User);
}

echo HTMLstuff::HtmlHeader('Users', $CurrentUser);

?>

<h2><?php echo sprintf('<a href="index.php">Home</a> - Users'); ?></h2>

<table border="0" cellpadding="4" cellspacing="0">
	<thead>
		<tr>
			<th style="width: 160px;">Username</th>
			<th>Full name</th>
			<th style="width: 70px;">Gender</th>
			<th style="width: 120px;">Birthdate</th>
			<th style="width: 120px;">Last active</th>
			<th style="width: 120px;">Last login</th>
			<th style="width: 22px;">&nbsp;</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="7">Total user count: <?php echo sprintf("%1\$d", $UserCount); ?></th>
		</tr>
	</tfoot>
	<tbody>
	<?php echo $UserRows ? $UserRows : '<tr class="Row1"><td colspan="7">&nbsp;</td></tr>'; ?>
	</tbody>
</table>

<?php
echo HTMLstuff::Button(sprintf('user_view.php'), 'New user');
echo HTMLstuff::Button();
echo HTMLstuff::HtmlFooter();
?>