<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();
HTMLstuff::RefererRegister($_SERVER['REQUEST_URI']);

if(!$CurrentUser->hasPermission(RIGHT_SEARCH_TAGS))
{
	$e = new Error(RIGHTS_ERR_USERNOTALLOWED);
	Error::AddError($e);
	
	header('location:index.php');
	exit;
}

$Tags = Tag::GetTags();

$TotalPages = 0;
$Total = NULL;
$searchMode = NULL;
$q = NULL;
$page = 1;
$max_results = 30;
$ToShow = array();
$Tag2Alls = array();
$ItemCount = 0;
$Results = '';


/* Querystring and session-processing */
if (isset($_GET['p']))
{
	$page = Utils::SafeIntFromQS('p');
	$page = $page <= 0 ? 1 : $page;
}

if (isset($_GET['x']))
{
	$max_results = Utils::SafeIntFromQS('x');
	$max_results <= 0 ? 30 : $max_results;
}

$from = (($page * $max_results) - $max_results);

$q = array_key_exists('q', $_GET) ? $_GET['q'] : (array_key_exists('q', $_SESSION) ? $_SESSION['q'] : '');
$_SESSION['q'] = $q;

$searchMode = array_key_exists('t', $_GET) ? $_GET['t'] : (array_key_exists('t', $_SESSION) ? $_SESSION['t'] : 'SET');
$_SESSION['t'] = $searchMode; 


/* Core search-processing */
$filteredTags = Tag::FilterByCSV($Tags, $q);
$filteredTagIDs = array();

foreach($filteredTags as $t){
	$filteredTagIDs[] = $t->getID();
}

/* Fetch Tag2Alls if tags were entered, default to no results when $q is empty. */
$Tag2Alls = $filteredTagIDs ? Tag2All::GetTag2Alls(new Tag2AllSearchParameters(FALSE, $filteredTagIDs)) : array();


/* Model-filtering */
$AllModelIDs = array();
foreach($Tag2Alls as $t2a){
	$AllModelIDs[] = $t2a->getModelID();
}

$ModelIDsToShow = array();
foreach(array_unique($AllModelIDs) as $modelid)
{
	foreach ($filteredTagIDs as $ftid)
	{
		if(count(Tag2All::Filter($Tag2Alls, $ftid, $modelid, FALSE, FALSE, FALSE)) == 0)
		{ continue 2; }
	}
	if(!is_null($modelid))
	{ $ModelIDsToShow[] = $modelid; }
}

/* Set-filtering */
$AllSetIDs = array();
foreach($Tag2Alls as $t2a){
	$AllSetIDs[] = $t2a->getSetID();
}

$SetIDsToShow = array();
foreach(array_unique($AllSetIDs) as $setid)
{
	foreach ($filteredTagIDs as $ftid)
	{
		if(count(Tag2All::Filter($Tag2Alls, $ftid, FALSE, $setid, FALSE, FALSE)) == 0)
		{ continue 2; }
	}
	if(!is_null($setid))
	{ $SetIDsToShow[] = $setid; }
}


/* Image-filtering */
$AllImageIDs = array();
foreach($Tag2Alls as $t2a){
	$AllImageIDs[] = $t2a->getImageID();
}

$ImageIDsToShow = array();
foreach(array_unique($AllImageIDs) as $imageid)
{
	foreach ($filteredTagIDs as $ftid)
	{
		if(count(Tag2All::Filter($Tag2Alls, $ftid, FALSE, FALSE, $imageid, FALSE)) == 0)
		{ continue 2; }
	}
	if(!is_null($imageid))
	{ $ImageIDsToShow[] = $imageid; }
}

/* Video-filtering */
$AllVideoIDs = array();
foreach($Tag2Alls as $t2a){
	$AllVideoIDs[] = $t2a->getVideoID();
}

$VideoIDsToShow = array();
foreach(array_unique($AllVideoIDs) as $videoid)
{
	foreach ($filteredTagIDs as $ftid)
	{
		if(count(Tag2All::Filter($Tag2Alls, $ftid, FALSE, FALSE, FALSE, $videoid)) == 0)
		{ continue 2; }
	}
	if(!is_null($videoid))
	{ $VideoIDsToShow[] = $videoid; }
}

switch ($searchMode)
{
	case 'MODEL':
		if(!$ModelIDsToShow){ break; }
		$ToShow = Model::GetModels(new ModelSearchParameters(FALSE, $ModelIDsToShow), NULL, sprintf("%1\$d, %2\$d", $from, $max_results));
		$Total = count(Model::GetModels(new ModelSearchParameters(FALSE, $ModelIDsToShow)));
		break;

	case 'SET':
		if(!$SetIDsToShow || !$ModelIDsToShow){ break; }
		$ssp = new SetSearchParameters(FALSE, $SetIDsToShow);
		$ToShow = Set::GetSets($ssp, NULL, sprintf("%1\$d, %2\$d", $from, $max_results));
		$Total = count(Set::GetSets($ssp));
		break;

	case 'IMAGE':
		if(!$SetIDsToShow || !$ModelIDsToShow){ break; }
		$isp = new ImageSearchParameters(FALSE, $ImageIDsToShow, FALSE, $SetIDsToShow, FALSE, FALSE, TRUE);
		$ToShow = Image::GetImages($isp, NULL, sprintf("%1\$d, %2\$d", $from, $max_results));
		$Total = count(Image::GetImages($isp));
		break;

	case 'VIDEO':
		if(!$SetIDsToShow || !$ModelIDsToShow){ break; }
		$vsp = new VideoSearchParameters(FALSE, $VideoIDsToShow, FALSE, $SetIDsToShow, FALSE, FALSE, TRUE);
		$ToShow = Video::GetVideos($vsp, NULL, sprintf("%1\$d, %2\$d", $from, $max_results));
		$Total = count(Video::GetVideos($vsp));
		break;
}

$TotalPages = ceil($Total / $max_results);
if($ToShow)
{
	switch ($searchMode)
	{
		case "MODEL":
			foreach($ToShow as $Model)
			{
				/* @var $Model Model */
				$ItemCount++;
				$Results .= sprintf(
					"<div class=\"ThumbGalItem\">
						<h3 class=\"Hidden\">%1\$s</h3>
						<div class=\"ThumbImageWrapper\">
							<a href=\"set.php?model_id=%2\$d\" title=\"%1\$s\">
								<img src=\"download_image.php?model_id=%2\$d&amp;portrait_only=true&amp;width=150&amp;height=225\" width=\"150\" height=\"225\" alt=\"%1\$s\" />
							</a>
						</div>
						<div class=\"SearchThumbDataWrapper\">
							<ul>
								<li>%4\$s: %1\$s</li>
							</ul>
						</div>
					</div>
					%3\$s",
					htmlentities($Model->GetFullName()),
					$Model->getID(),
					($ItemCount % 4 == 0 ? "<div class=\"Clear\"></div>" : NULL),
					$lang->g('LabelName')
				);
			}
			break;

		case "SET":
			foreach($ToShow as $Set)
			{
				/* @var $Set Set */
				$ItemCount++;
				$Results .= sprintf(
					"<div class=\"SetThumbGalItem\">
						<h3 class=\"Hidden\">%1\$s set %3\$s</h3>
						<div class=\"SetThumbImageWrapper\">
							<a href=\"image.php?model_id=%4\$d&amp;set_id=%5\$d\" title=\"%1\$s set %3\$s\">
								<img src=\"download_image.php?set_id=%5\$d&amp;landscape_only=true&amp;width=225&amp;height=150\" height=\"150\" alt=\"%1\$s set %3\$s\" />
							</a>
						</div>
						<div class=\"SearchThumbDataWrapper\">
							<ul>
								<li>%7\$s: %1\$s</li>
								<li>%8\$s: %3\$s</li>
							</ul>
						</div>
					</div>
					%6\$s",
					htmlentities($Set->getModel()->GetFullName()),
					htmlentities($Set->getPrefix()),
					htmlentities($Set->getName()),
					$Set->getModel()->getID(),
					$Set->getID(),
					($ItemCount % 3 == 0 ? "<div class=\"Clear\"></div>" : NULL),
					$lang->g('NavigationModel'),
					$lang->g('NavigationSet')
				);
			}
			break;

		case "IMAGE":
			foreach($ToShow as $Image)
			{
				/* @var $Image Image */
				$ItemCount++;
				$Results .= sprintf(
					"<div class=\"SetThumbGalItem\">
						<h3 class=\"Hidden\">%4\$s.%5\$s</h3>
						<div class=\"SetThumbImageWrapper\">
							<a href=\"image_view.php?model_id=%6\$d&amp;set_id=%7\$d&amp;image_id=%8\$d\" title=\"%4\$s.%5\$s\">
								<img src=\"download_image.php?image_id=%8\$d&amp;width=225&amp;height=150\" height=\"150\" alt=\"%4\$s.%5\$s\" />
							</a>
						</div>
						<div class=\"SearchThumbDataWrapper\">
							<ul>
								<li>%4\$s.%5\$s</li>
							</ul>
						</div>
					</div>
					%9\$s",
					htmlentities($Image->getSet()->getModel()->GetFullName()),
					htmlentities($Image->getSet()->getPrefix()),
					htmlentities($Image->getSet()->getName()),
					htmlentities($Image->getFileName()),
					htmlentities($Image->getFileExtension()),
					$Image->getSet()->getModel()->getID(),
					$Image->getSet()->getID(),
					$Image->getID(),
					($ItemCount % 3 == 0 ? "<div class=\"Clear\"></div>" : NULL)
				);
			}
			break;

		case "VIDEO":
			foreach($ToShow as $Video)
			{
				/* @var $Video Video */
				$ItemCount++;
				$Results .= sprintf(
					"<div class=\"SetThumbGalItem\">
						<h3 class=\"Hidden\">%4\$s.%5\$s</h3>
						<div class=\"SetThumbImageWrapper\">
							<a href=\"video_view.php?model_id=%6\$d&amp;set_id=%7\$d&amp;video_id=%8\$d\" title=\"%4\$s.%5\$s\">
								<img src=\"download_image.php?video_id=%8\$d&amp;width=225&amp;height=150\" height=\"150\" alt=\"%4\$s.%5\$s\" />
							</a>
						</div>
						<div class=\"SearchThumbDataWrapper\">
							<ul>
								<li>%4\$s.%5\$s</li>
							</ul>
						</div>
					</div>
					%9\$s",
					htmlentities($Video->getSet()->getModel()->GetFullName()),
					htmlentities($Video->getSet()->getPrefix()),
					htmlentities($Video->getSet()->getName()),
					htmlentities($Video->getFileName()),
					htmlentities($Video->getFileExtension()),
					$Video->getSet()->getModel()->getID(),
					$Video->getSet()->getID(),
					$Video->getID(),
					($ItemCount % 3 == 0 ? "<div class=\"Clear\"></div>" : NULL)
				);
			}
			break;
	}
}

echo HTMLstuff::HtmlHeader($lang->g('NavigationTagSearch'), $CurrentUser);

?>

<h2><a href="index.php"><?php echo $lang->g('NavigationHome')?></a> - <?php echo $lang->g('NavigationTagSearch')?></h2>

<form action="<?php echo htmlentities($_SERVER['REQUEST_URI'])?>" method="get" class="Search">
<fieldset>

<label for="t"><?php echo $lang->g('LabelSearchFor')?></label>
<select id="t" name="t">
	<option value="MODEL" <?php echo $searchMode == "MODEL" ? ' selected="selected"' : NULL ?>><?php echo $lang->g('NavigationModels')?></option>
	<option value="SET"   <?php echo $searchMode == "SET"   ? ' selected="selected"' : NULL ?>><?php echo $lang->g('NavigationSets')?></option>
	<option value="IMAGE" <?php echo $searchMode == "IMAGE" ? ' selected="selected"' : NULL ?>><?php echo $lang->g('NavigationImages')?></option>
	<option value="VIDEO" <?php echo $searchMode == "VIDEO" ? ' selected="selected"' : NULL ?>><?php echo $lang->g('NavigationVideos')?></option>
</select>

<label for="q"><?php echo $lang->g('LabelTaggedWith')?></label>
<input type="text" id="q" name="q" class="TagsBox" style="width:470px;" value="<?php echo $q?>" />
<input type="submit" class="FormButton" value="<?php echo $lang->g('ButtonSearch')?>" />
<label for="x"><?php echo $lang->g('LabelResultsPerPage')?></label>
<input type="text" id="x" name="x" class="TagsBox" style="width:50px;" value="<?php echo $max_results?>" />

<div class="Separator"></div>

<div class="Clear"></div>
<?php echo $Results ?>
<div class="Clear"></div>

<div class="SearchSummary">
<?php
if($page > 1)
{
	echo HTMLStuff::Button('search.php?p=1&amp;t='.$searchMode.'&amp;q='.$q.'&amp;x='.$max_results, $lang->g('ButtonPageFirst'), ' style="float:left"');
	echo HTMLStuff::Button('search.php?p='.($page-1).'&amp;t='.$searchMode.'&amp;q='.$q.'&amp;x='.$max_results, $lang->g('ButtonPagePrevious'), ' style="float:left"');
}

if($page < $TotalPages)
{
	echo HTMLStuff::Button('search.php?p='.($TotalPages).'&amp;t='.$searchMode.'&amp;q='.$q.'&amp;x='.$max_results, $lang->g('ButtonPageLast'), ' style="float:right"');
	echo HTMLStuff::Button('search.php?p='.($page+1).'&amp;t='.$searchMode.'&amp;q='.$q.'&amp;x='.$max_results, $lang->g('ButtonPageNext'), ' style="float:right"');
}
?>
<div class="Clear"></div>

<?php
echo sprintf($lang->g('LabelShowingXResults'),
	$Total ? sprintf($lang->g('LabelXtoYofZ'), ($from+1), (($from+$max_results > $Total) ? $Total : $from+$max_results), $Total) : '0'
);
?>

</div>

<div class="Clear"></div>

<?php echo HTMLstuff::Button('index.php')?>

</fieldset>
</form>

<?php
echo HTMLstuff::HtmlFooter($CurrentUser);
?>