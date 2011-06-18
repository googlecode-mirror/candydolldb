<?php

include('cd.php');
ini_set('max_execution_time', '3600');
$CurrentUser = Authentication::Authenticate();

$OutputSql = array_key_exists('output', $_GET) && isset($_GET['output']) && is_numeric($_GET['output']) && (int)$_GET['output'] === 1;

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
	
	/* @var $folder SplFileInfo */
	foreach(new DirectoryIterator($ImageFolder) as $folder)
	{
		if($folder->isDir() && $folder->isReadable() && !$folder->isDot())
		{
			$ImageSetFolder = $folder->getRealPath();
			
			$name = $folder->getFilename();
			$setnamematch = preg_match('/([^_]*\d\d[a-z]?)$/i', $name, $matches);
			$setname = $matches && $matches > 0 ? $matches[1] : null;
			
			if($SetID && $Set)
			{
				if($Set->getName() != $setname)
				{ continue; }
			}

			if($setname)
			{
				$Set = Set::FilterSets($Sets, $Model->getID());
				$Set = Set::FilterSets($Set, null, null, $setname);
				
				if($Set)
				{ $Set = $Set[0]; }
				else
				{
					$Set = new Set(null, $setname);
					$Set->setModel($Model);
					$Set->setContainsWhat(SET_CONTENT_IMAGE | SET_CONTENT_VIDEO);
					
					if(strlen($setname) == 2)
					{ $Set->setPrefix('set_'); }
					else
					{ $Set->setPrefix('SP_'); }

					Set::InsertSet($Set, $CurrentUser);
					$setid = $db->GetLatestID();
					if(	$setid) { $Set->setID($setid); }
				}
			}
						
			/* @var $FileInfo SplFileInfo */
			foreach(new DirectoryIterator($ImageSetFolder) as $FileInfo)
			{
				if($FileInfo->isFile() && $FileInfo->isReadable())
				{
					/* @var $ImageInDB Image */
					$info = getimagesize($FileInfo->getRealPath());
				
					$ImagesInDB = Image::FilterImages(
						$Images,
						$ModelID,
						$Set->getID(),
						str_ireplace(IMAGE_EXTENSION, '', $FileInfo->getFilename())
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
					
					$ImageInDB->setFileName($FileInfo->getBasename(IMAGE_EXTENSION));
					$ImageInDB->setFileExtension(trim(IMAGE_EXTENSION, '.'));
					$ImageInDB->setFileSize($FileInfo->getSize());
					$ImageInDB->setFileCheckSum(md5_file($FileInfo->getRealPath()));
					$ImageInDB->setImageWidth($info[0]);
					$ImageInDB->setImageHeight($info[1]);
					$ImageInDB->setDateTaken($FileInfo->getCTime());
					
					if(!$ImageInDB->getID())
					{ Image::InsertImage($ImageInDB, $CurrentUser); }
					else
					{ Image::UpdateImage($ImageInDB, $CurrentUser); }
				}
			}
		}
	}
}

if(array_key_exists('HTTP_REFERER', $_SERVER) && $_SERVER['HTTP_REFERER'])
{ header('location:'.$_SERVER['HTTP_REFERER']); }
else 
{ header('location:index.php'); }

?>