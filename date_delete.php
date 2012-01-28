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
$CurrentUser = Authentication::Authenticate();

$DateID = null;

if(array_key_exists('date_id', $_GET) && isset($_GET['date_id']) && is_numeric($_GET['date_id']))
{
	$DateID = (int)$_GET['date_id'];
	Date::DeleteDate(new Date($DateID), $CurrentUser);
}

HTMLstuff::RefererRedirect();

?>