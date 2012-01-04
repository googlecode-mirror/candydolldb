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


$ModelID = null;
$SetID = null;


if(array_key_exists('model_id', $_GET) && isset($_GET['model_id']) && is_numeric($_GET['model_id']))
{ $ModelID = (int)$_GET['model_id']; }

if(array_key_exists('set_id', $_GET) && isset($_GET['set_id']) && is_numeric($_GET['set_id']))
{ $SetID = (int)$_GET['set_id']; }


$Models = Model::GetModels(
	sprintf(
		'model_id = IFNULL(%1$s, model_id) AND mut_deleted = -1',
		$ModelID ? (string)$ModelID : 'NULL'
	)
);

$Sets = Set::GetSets(
	sprintf(
		'set_id = IFNULL(%1$s, set_id) AND mut_deleted = -1',
		$SetID ? (string)$SetID : 'NULL'
	)
);

$Videos = Video::GetVideos(
	sprintf(
		'model_id = IFNULL(%1$s, model_id) AND set_id = IFNULL(%2$s, set_id) AND mut_deleted = -1',
		$ModelID ? (string)$ModelID : 'NULL',
		$SetID ? (string)$SetID : 'NULL'
	)
);

$CacheImages = CacheImage::GetCacheImages();


if($SetID){
	$Set = $Sets[0];
	$Models = array($Set->getModel());
}


/* @var $Model Model */
for($i = 0; $i < count($Models); $i++)
{
	$Model = $Models[$i];

	$VideoFolder = sprintf('%1$s/%2$s',
		CANDYVIDEOPATH,
		$Model->GetFullName()
	);

	if(!file_exists($VideoFolder)) { continue; }
	
	/* @var $it RecursiveIteratorIterator */
	$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(
			$VideoFolder,	
		 	FileSystemIterator::SKIP_DOTS | FileSystemIterator::CURRENT_AS_FILEINFO));

	$itArray = array();
	foreach($it as $file)
	{ $itArray[] = $file; }
	
	if(isset($argv) && $argc > 0)
	{ $bi = new BusyIndicator(count($itArray), 0, sprintf('%1$2d/%2$2d %3$s', ($i + 1), count($Models), $Model->GetShortName())); }


	/* @var $FileInfo SplFileInfo */
	foreach($itArray as $FileInfo)
	{
		if(isset($argv) && $argc > 0)
		{ $bi->Next(); }
		
		if($FileInfo->isFile() && $FileInfo->isReadable())
		{
			$setnamematch = preg_match('/(?P<Prefix>[A-Z]+[_ -])?(?P<Name>[A-Z0-9]+)(?P<Number>\d\d)(?P<Suffix>[a-z])?\.(?P<Extension>[^.]+)$/i', $FileInfo->getFilename(), $matches);
			
			if(isset($matches) && count($matches) > 0)
			{
				$Set = Set::FilterSets($Sets, $Model->getID(), null, ($matches['Name'].$matches['Number']), $matches['Prefix']);

				if(!$Set)
				{ continue; }
				else
				{ $Set = $Set[0]; }
			
				/* @var $VideoInDB Video */
				$VideosInDB = Video::FilterVideos($Videos, $ModelID, $Set->getID(), $matches['Name'].$matches['Number'].$matches['Suffix']);

				if($VideosInDB)
				{
					$VideoInDB = $VideosInDB[0];
					
					$CacheImage = CacheImage::FilterCacheImages($CacheImages, null, null, null, null, null, $VideoInDB->getID());
					if(count($CacheImage) > 0){
						CacheImage::DeleteImage($CacheImage[0], $CurrentUser);
					}
				}
				else
				{
					$VideoInDB = new Video();
					$VideoInDB->setSet($Set);
				}
				
				$VideoInDB->setFileName($matches['Prefix'].$matches['Name'].$matches['Number'].$matches['Suffix']);
				$VideoInDB->setFileExtension($matches['Extension']);
				$VideoInDB->setFileSize($FileInfo->getSize());
				$VideoInDB->setFileCheckSum(md5_file($FileInfo->getRealPath()));
					
				if(!$VideoInDB->getID())
				{ Video::InsertVideo($VideoInDB, $CurrentUser); }
				else
				{ Video::UpdateVideo($VideoInDB, $CurrentUser); }
			}
		}
	}
}

if(!isset($argv) || !$argc)
{ HTMLstuff::RefererRedirect(); }

?>