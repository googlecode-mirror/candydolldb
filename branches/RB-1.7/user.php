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
			"<td class=\"Center\"><a href=\"user_view.php?user_id=%1\$d&amp;cmd=%9\$s\" title=\"%11\$s\"><img src=\"images/button_delete.png\" width=\"16\" height=\"16\" alt=\"%11\$s\" /></a></td>".
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
		$UserCount % 2 == 0 ? 2 : 1,
		$lang->g('LabelDeleteUser')
		);
	}
	unset($User);
}

echo HTMLstuff::HtmlHeader($lang->g('NavigationUsers'), $CurrentUser);

?>

<h2><?php echo sprintf('<a href="index.php">%2$s</a> - %1$s',
	$lang->g('NavigationUsers'),
	$lang->g('NavigationHome')
)?></h2>

<table border="0" cellpadding="4" cellspacing="0">
	<thead>
		<tr>
			<th style="width: 160px;"><?php echo $lang->g('LabelUsername')?></th>
			<th><?php echo $lang->g('LabelFullName')?></th>
			<th style="width: 70px;"><?php echo $lang->g('LabelGender')?></th>
			<th style="width: 120px;"><?php echo $lang->g('LabelBirthdate')?></th>
			<th style="width: 120px;"><?php echo $lang->g('LabelLastActive')?></th>
			<th style="width: 120px;"><?php echo $lang->g('LabelLastLogin')?></th>
			<th style="width: 22px;">&nbsp;</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="7"><?php echo sprintf("%1\$s: %2\$d", $lang->g('LabelTotalUserCount'), $UserCount)?></th>
		</tr>
	</tfoot>
	<tbody>
	<?php echo $UserRows ? $UserRows : '<tr class="Row1"><td colspan="7">&nbsp;</td></tr>'?>
	</tbody>
</table>

<?php
echo HTMLstuff::Button(sprintf('user_view.php'), $lang->g('LabelNewUser'));
echo HTMLstuff::Button();
echo HTMLstuff::HtmlFooter($CurrentUser);
?>