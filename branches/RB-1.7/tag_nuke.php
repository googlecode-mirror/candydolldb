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

include('cd.php');
ini_set('max_execution_time', '3600');
$CurrentUser = Authentication::Authenticate();

$Tags = Tag::GetTags();
$Tag2Alls = Tag2All::GetTag2Alls();

/* @var $t Tag */
/* @var $t2a Tag2All */
foreach($Tags as $t)
{
	$t2a = Tag2All::FilterTag2Alls($Tag2Alls, $t->getID(), FALSE, FALSE, FALSE, FALSE);
	
	if(!$t2a){
		Tag::DeleteTag($t, $CurrentUser);
	}
}

HTMLstuff::RefererRedirect();

?>