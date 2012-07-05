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


$ImageID = Utils::SafeIntFromQS('image_id');
$VideoID = Utils::SafeIntFromQS('video_id');
$SetID = Utils::SafeIntFromQS('set_id');
$ModelIndexID = Utils::SafeIntFromQS('index_id');
$ModelID = Utils::SafeIntFromQS('model_id');
$Width = Utils::SafeIntFromQS('width');
$Height = Utils::SafeIntFromQS('height');;
$WhereClause = null;


/* @var $CacheImage CacheImage */
$CacheImage = null;


if(isset($ImageID))
{
	$WhereClause = sprintf('image_id = %1$d', $ImageID);
}
else if(isset($VideoID))
{
	$WhereClause = sprintf('video_id = %1$d', $VideoID);
}
else if(isset($SetID))
{
	$WhereClause = sprintf('set_id = %1$d', $SetID);
}
else if(isset($ModelIndexID))
{
	$WhereClause = sprintf('index_id = %1$d', $ModelIndexID);
}
else if(isset($ModelID))
{
	$WhereClause = sprintf('model_id = %1$d', $ModelID);
}

if(!is_null($WhereClause) && isset($Width))
{
	$WhereClause .= sprintf(' AND cache_imagewidth = %1$d', $Width);
}

if(!is_null($WhereClause) && isset($Height))
{
	$WhereClause .= sprintf(' AND cache_imageheight = %1$d', $Height);
}

if(!is_null($WhereClause))
{
	$CacheImage = CacheImage::GetCacheImages($WhereClause);
}

if(!is_null($CacheImage))
{
	foreach($CacheImage as $CI)
	{ CacheImage::DeleteImage($CI, $CurrentUser); }
}

HTMLstuff::RefererRedirect();

?>