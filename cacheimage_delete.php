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
$CurrentUser = Authentication::Authenticate();

if(!$CurrentUser->hasPermission(RIGHT_CACHE_DELETE))
{
	$e = new Error(RIGHTS_ERR_USERNOTALLOWED);
	Error::AddError($e);
	HTMLstuff::RefererRedirect();
}

$ImageID = Utils::SafeIntFromQS('image_id');
$VideoID = Utils::SafeIntFromQS('video_id');
$SetID = Utils::SafeIntFromQS('set_id');
$ModelIndexID = Utils::SafeIntFromQS('index_id');
$ModelID = Utils::SafeIntFromQS('model_id');
$Width = Utils::SafeIntFromQS('width');
$Height = Utils::SafeIntFromQS('height');;

$DeleteSetsImages = Utils::SafeBoolFromQS('deleteimages');
$DeleteSetsVideos = Utils::SafeBoolFromQS('deletevideos');

$CacheImages = array();
$MultipleImageIDs = NULL;
$MultipleVideoIDs = NULL;

if(!is_null($SetID) && $DeleteSetsImages)
{
	$imagesThisSet = Image::GetImages(
		new ImageSearchParameters(FALSE, FALSE, $SetID)
	);
	
	/* @var $im Image */
	foreach ($imagesThisSet as $im)
	{ $MultipleImageIDs[] = $im->getID(); }
	
	$cisp = new CacheImageSearchParameters(
		FALSE, FALSE,
		FALSE, FALSE,
		FALSE, FALSE,
		FALSE, FALSE,
		FALSE, is_null($MultipleImageIDs) ? FALSE : $MultipleImageIDs
	);
	
	if($cisp->getValues())
	{ $CacheImages = array_merge($CacheImages, CacheImage::GetCacheImages($cisp)); }
}

if(!is_null($SetID) && $DeleteSetsVideos)
{
	$videosThisSet = Video::GetVideos(
		new VideoSearchParameters(FALSE, FALSE, $SetID)
	);

	/* @var $vd Video */
	foreach ($videosThisSet as $vd)
	{ $MultipleVideoIDs[] = $vd->getID(); }

	$cisp = new CacheImageSearchParameters(
		FALSE, FALSE,
		FALSE, FALSE,
		FALSE, FALSE,
		FALSE, FALSE,
		FALSE, FALSE,
		FALSE, is_null($MultipleVideoIDs) ? FALSE : $MultipleVideoIDs
	);

	if($cisp->getValues())
	{ $CacheImages = array_merge($CacheImages, CacheImage::GetCacheImages($cisp)); }
}

$cisp = new CacheImageSearchParameters(
	FALSE, FALSE,
	is_null($ModelIndexID) ? FALSE : $ModelIndexID,
	FALSE,
	is_null($ModelID) ? FALSE : $ModelID, FALSE,
	is_null($SetID) ? FALSE : $SetID, FALSE,
	is_null($ImageID) ? FALSE : $ImageID, FALSE,
	is_null($VideoID) ? FALSE : $VideoID, FALSE,
	is_null($Width) ? FALSE : $Width, is_null($Height) ? FALSE : $Height
);

if($cisp->getValues())
{ $CacheImages = array_merge($CacheImages, CacheImage::GetCacheImages($cisp)); }

if($CacheImages)
{ CacheImage::DeleteMulti($CacheImages, $CurrentUser); } 

HTMLstuff::RefererRedirect();

?>
