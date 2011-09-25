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

$Images = Image::GetImages(
	sprintf(
		'model_id = IFNULL(%1$s, model_id) AND set_id = IFNULL(%2$s, set_id) AND mut_deleted = -1',
		$ModelID ? (string)$ModelID : 'NULL',
		$SetID ? (string)$SetID : 'NULL'
	)
);


if($SetID && $Sets){
	$Set = $Sets[0];
	$Models = array($Set->getModel());
}


/* @var $Model Model */
for($i = 0; $i < count($Models); $i++)
{
	$Model = $Models[$i];

	$ImageFolder = sprintf('%1$s/%2$s',
		CANDYIMAGEPATH,
		$Model->GetFullName()
	);
	
	if(!file_exists($ImageFolder)) { continue; }
	
	/* @var $it RecursiveIteratorIterator */
	$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(
			$ImageFolder,	
		 	FileSystemIterator::SKIP_DOTS | FileSystemIterator::CURRENT_AS_FILEINFO));
	

	$itArray = array();
	foreach($it as $file)
	{ $itArray[] = $file; }
	
	if(isset($argv) && $argc > 0)
	{ $bi = new BusyIndicator(count($itArray), 0, sprintf('%1$2d/%2$2d %3$s', ($i + 1), count($Models), $Model->GetShortName())); }

		 	
	/* @var $file SplFileInfo */
	foreach($itArray as $file)
	{
		if(isset($argv) && $argc > 0)
		{ $bi->Next(); }
		
		if($file->isFile() && $file->isReadable())
		{
			$imagenamematch = preg_match('/(?P<Prefix>[A-Z]+[_ -])?(?P<ModelName>[A-Z0-9]+)(?P<SetNumber>\d\d)_(?P<Number>[0-9]{3})\.(?P<Extension>[^.]+)$/i', $file->getFilename(), $matches);
			
			if($imagenamematch)
			{
				$Set = Set::FilterSets($Sets, $Model->getID(), null, ($matches['ModelName'].$matches['SetNumber']), $matches['Prefix']);

				if($Set)
				{ $Set = $Set[0]; }
				else
				{ continue; }

				/* @var $ImageInDB Image */
				$ImagesInDB = Image::FilterImages(
					$Images,
					$ModelID,
					$Set->getID(),
					sprintf('%1$s%2$s%3$s_%4$s', $matches['Prefix'], $matches['ModelName'], $matches['SetNumber'], $matches['Number'])
				);

				if($ImagesInDB)
				{
					$ImageInDB = $ImagesInDB[0];
				}
				else
				{
					$ImageInDB = new Image();
					$ImageInDB->setSet($Set);
				}
				
				$info = getimagesize($file->getRealPath());

				$ImageInDB->setFileName(sprintf('%1$s%2$s%3$s_%4$s', $matches['Prefix'], $matches['ModelName'], $matches['SetNumber'], $matches['Number']));
				$ImageInDB->setFileExtension($matches['Extension']);
				$ImageInDB->setFileSize($file->getSize());
				$ImageInDB->setFileCheckSum(md5_file($file->getRealPath()));
				$ImageInDB->setImageWidth($info[0]);
				$ImageInDB->setImageHeight($info[1]);
					
				if(!$ImageInDB->getID())
				{ Image::InsertImage($ImageInDB, $CurrentUser); }
				else
				{ Image::UpdateImage($ImageInDB, $CurrentUser); }
			}
		}
	}
}

if(!isset($argv) || !$argc)
{ HTMLstuff::RefererRedirect(); }

?>