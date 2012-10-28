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

include('cd.php');
ini_set('max_execution_time', '3600');
$CurrentUser = Authentication::Authenticate();

if($CurrentUser->hasPermission(RIGHT_TAG_CLEANUP))
{
	$Tags = Tag::GetTags();
	
	/* @var $t Tag */
	foreach($Tags as $t)
	{
		$t2as = Tag2All::GetTag2Alls(new Tag2AllSearchParameters($t->getID()));
		
		if(!$t2as){
			Tag::Delete($t, $CurrentUser);
		}
	}
	
	$infoSuccess = new Info($lang->g('MessageTagsCleaned'));
	Info::AddInfo($infoSuccess);
}
else
{
	$e = new Error(RIGHTS_ERR_USERNOTALLOWED);
	Error::AddError($e);
}

HTMLstuff::RefererRedirect();

?>
