<?php

# get correct id for plugin
$thisfile = basename(__FILE__, ".php");

# register plugin
register_plugin(
	$thisfile, 
	'SEO Manager', 	
	'1.0', 		
	'Mike Henken',
	'http://michaelhenken.com', 
	'Manage SEO process',
	'pages',
	'process_seo'  
);

add_action('pages-sidebar','createSideMenu',array($thisfile,'SEO Settings'));
add_action('theme-footer','return_footer');
define('SEOFile', GSDATAOTHERPATH  . 'seo.xml');
define('SEOPath', GSDATAPATH  . 'seo/');

require_once(GSPLUGINPATH."website_seo/class/seo_class.php");

function process_seo()
{
	showHeader();
	ShowForm();
}


function showHeader()
{
	?>
	<div style="width:100%;margin:0 -15px -15px -10px;padding:0px;">
		<h3 class="floated">SEO</h3>
		<div class="edit-nav clearfix" style="">
			<a href="load.php?id=website_seo&seo_help" <?php if (isset($_GET['seo_help'])) { echo 'class="current"'; } ?>>?</a>
			<a href="load.php?id=website_seo&seo_notices" <?php if (isset($_GET['seo_notices'])) { echo 'class="current"'; } ?>>Notices</a>
			<a href="load.php?id=website_seo&seo_reports" <?php if (isset($_GET['seo_reports'])) { echo 'class="current"'; } ?>>Reports</a>
			<a href="load.php?id=website_seo&seo_bookmarking" <?php if (isset($_GET['seo_bookmarking'])) { echo 'class="current"'; } ?>>Check List</a>
			<a href="load.php?id=website_seo&seo_footer" <?php if (isset($_GET['seo_footer'])) { echo 'class="current"'; } ?>>HP Footer</a>
			<a href="load.php?id=website_seo&seo_descriptions" <?php if (isset($_GET['seo_descriptions'])) { echo 'class="current"'; } ?>>Descriptions</a>
			<a href="load.php?id=website_seo&seo_keyphrases" <?php if (isset($_GET['seo_keyphrases'])) { echo 'class="current"'; } ?>>Keyphrases</a>
		</div> 
		<style>
			h4 {
				width:918px !important;
				font-size: 16px;
				font-weight: normal;
				font-family: Georgia, Times, Times New Roman, serif;
				color: #CF3805;
				margin-bottom:10px !important;
			}
		</style>

	</div>
	</div>
	<div class="main" style="margin-top:-10px;">
	<?php
}

function ShowForm()
{
	$Submit_SEO = new GetSEOdata;
	if(isset($_GET['seo_keyphrases']))
	{
		showKeyphrases();
	}
	elseif(isset($_GET['seo_descriptions']))
	{	
		showDescriptions(); 
	}
	elseif(isset($_GET['seo_bookmarking']))
	{
		showSocialBookmarking();
	}
	elseif(isset($_GET['seo_footer']))
	{
		showFooter();
	}
	elseif(isset($_GET['seo_reports']))
	{
		if(isset($_POST['upload_report']))
		{
			$Submit_SEO->uploadReport();
		}
		showReports();
		showNotes();
	}
	elseif(isset($_GET['seo_help']))
	{
		showHelp();
	}
	elseif(isset($_GET['seo_notices']))
	{
		showNotices();
	}
	else
	{
		showKeyphrases();
	}
	?>
	<?php
}

function showKeyphrases()
{
	$currentKeyPhrase = 0;
	$Submit_SEO = new GetSEOdata;
	if(isset($_POST['keyphrase']))
	{
		$array = $_POST['keyphrase'];
		if(isset($_POST['add_keyphrase']) && !empty($_POST['add_keyphrase']))
		{
			array_push($array, $_POST['add_keyphrase']);
		}
		$Submit_SEO->ProcessSEO($array, 'KEYPHRASES');
	}
	elseif(isset($_GET['delete_keyphrase']))
	{
		$Submit_SEO->ProcessSEO(null, 'KEYPHRASES', $_GET['delete_keyphrase']);
	}
	?>
	<div id="seo_keyphrases">
	<h3 class="floated">Choose 5 Keyphrases</h3>
	<form class="largeform" action="load.php?id=website_seo&submit_phrases" method="post" accept-charset="utf-8">
		<div class="edit-nav clearfix">
			<p>
				<a href="#" id="add-keyphrase">Add New Keyphrase</a>
			</p>
		</div>
		<div style="clear:both"></div>
		<!-- ADD NEW KEYPHRASE FORM -->
		<div id="profile" class="hide-div section" style="display:none;margin-top:-30px;background-color:#F6F6F6;padding:10px;width:90%;margin:15px;">
				<h3>Add New Keyphrase</h3>
				<div class="leftsec">
					<p>
						<label for="usernamec" >Keyphrase: </label>
						<input class="text" id="add_keyphrase" name="add_keyphrase" type="text" value="" />
					</p>
				</div>
				<div class="clear"></div>
				<p id="submit_line" >
					<span>
						<input class="submit" type="submit" name="submitted" value="Submit" />
					</span> &nbsp;&nbsp;<?php i18n('OR'); ?>&nbsp;&nbsp; 
					<a class="cancel" href="#"><?php i18n('CANCEL'); ?></a>
				</p>
		</div>
		<!-- END ADD NEW KEYPHRASE FORM -->

		<?php
		$keyphrase_file = getXML(SEOFile); 
		$count = 0;
		foreach($keyphrase_file->KEYPHRASES->KEYPHRASE as $key) 
		{
			$count++;
			?>
			<p>
				<label for="field-5"><?php echo 'Keyphrase '.$count; ?>: &nbsp;&nbsp; <code style="float:right;"><?php highlight_string('<?php get_keyphrase('.$count.'); ?> '); ?> &nbsp; <a href="load.php?id=website_seo&delete_keyphrase=<?php echo $count; ?>" title="Delete Keyphrase <?php echo $count; ?>?">X</a></code></label>
				<input type="text" class="text" name="keyphrase[]" style="width:99%" value="<?php echo $key; ?>" />
			</p>
			<?php
		}
		?>
	</div>
	<input type="hidden" name="KEYPHRASES" value="yes" />
	<p><input type="submit" class="submit" value="submit" /></p>
</form >
	<script type="text/javascript">
		
		/*** Show add-user form ***/
		$("#add-keyphrase").click(function () {
			$(".hide-div").show();
			$("#add-keyphrase").hide();
		});
		
		/*** Hide user form ***/
		$(".cancel").click(function () {
			$(".hide-div").hide();
			$("#add-keyphrase").show();
		});
	</script>
	<?php
}
function showHelp()
{
	?>
	<div id="seo_help">
		<h4>Inserting Keyphrases:</h4>
		<strong>Place the following php throughout websites template files.</strong><br/><br/>
		<?php highlight_string('<?php get_keyphrase(5); ?>'); ?><br/><br/>
		In the above php code, the number '5' represents the keyphrase # to display.<br/><br/>
	</div>
	<script type="text/javascript">
		$(document).ready(function() {
			$('.how_to').click(function() {
				$('#seo_help').show();
				$('.how_to').hide();
			})
		})
	</script>
	<?php
}

function showDescriptions()
{
	$Submit_SEO = new GetSEOdata;

	if(isset($_POST['SITEINFO']))
	{
		$array = array('TITLE'=> $_POST['TITLE'], 'SDESC'=> $_POST['SDESC'], 'LDESC'=> $_POST['LDESC'], 'ARTICLES'=> $_POST['ARTICLES']);
		$Submit_SEO->ProcessSEO($array, 'SITEINFO');
	}
?>	
	<style type="text/css">
		.seo_textarea {
			height:150px;
		}
		.seo_textarea_large {
			height:250px;
		}
	</style>
		<script type="text/javascript" src="../plugins/website_seo/js/script.js"></script>
	<h3>Website SEO Title &amp; Descriptions</h3>
	<p>
		You can write out your websites descriptions and title here for referance purposes.
	</p>
	<form class="largeform" action="load.php?id=website_seo&seo_descriptions" method="post" accept-charset="utf-8">
		<p>
			<label>Website SEO Title: &nbsp;&nbsp; <code style="float:right;"><?php highlight_string('<?php get_seo(\'TITLE\'); ?> '); ?></code> &nbsp; </label>
			<input type="text" class="text seo_title" style="width: 635px;" name="TITLE" value="<?php echo $Submit_SEO->SEOdata('SITEINFO','TITLE'); ?>" />
		</p>
		<p>
			<label>Short Description: &nbsp;&nbsp; <code style="float:right;"><?php highlight_string('<?php get_seo(\'SDESC\'); ?> '); ?></code> &nbsp; </label>
			<textarea class="seo_textarea" id="countShort" name="SDESC"><?php echo $Submit_SEO->SEOdata('SITEINFO','SDESC'); ?></textarea>
		</p>
		<p>
			<label>Long Description: &nbsp;&nbsp; <code style="float:right;"><?php highlight_string('<?php get_seo(\'LDESC\'); ?> '); ?></code> &nbsp; </label>
			<textarea class="seo_textarea" id="countLong" name="LDESC"><?php echo $Submit_SEO->SEOdata('SITEINFO','LDESC'); ?></textarea>
		</p>
		<p>
			<label>Article(s): &nbsp;&nbsp; <code style="float:right;"><?php highlight_string('<?php get_seo(\'Article\'); ?> '); ?></code> &nbsp; </label>
			<textarea class="seo_textarea_large" name="ARTICLES"><?php echo $Submit_SEO->SEOdata('SITEINFO','ARTICLES'); ?></textarea>
		</p>
	<input type="hidden" name="SITEINFO" value="yes" />
	<p><input type="submit" class="submit" value="submit" /></p>
	</form>
<?php
}

function showSocialBookmarking()
{
	global $SITEURL;
	$Submit_SEO = new GetSEOdata;
	if($_SERVER['REQUEST_METHOD'] == "POST")
	{
		$array = array();
		$count = 0;
		foreach($_POST as $name)
		{
			if($name != 'CHECKLIST' && $name[0] != '')
			{
				$count++;
				$array[$count]['NAME'] = $name[0];
				if(isset($name[1]))
				{
					$array[$count]['LINK'] = $name[1];
				}
				else
				{
					$array[$count]['LINK'] = '';
				}
				if(isset($name[2]))
				{
					$array[$count]['STATUS'] = $name[2];
				}
				else
				{
					$array[$count]['STATUS'] = '';
				}
			}
		}
		$Submit_SEO->ProcessSEO($array, 'CHECKLIST');
	}
	elseif(isset($_GET['delete_item']))
	{
		$Submit_SEO->ProcessSEO(null, 'CHECKLIST', $_GET['delete_item']);
	}
	
?>			
	<div id="seo_social">
	<h3 class="floated">SEO Checklist</h3>
		<div class="edit-nav clearfix">
			<p>
				<a href="#" id="add-keyphrase">Add New Checklist Item</a>
			</p>
		</div>
	<form class="largeform" action="load.php?id=website_seo&seo_bookmarking" method="POST">

	<!-- ADD NEW CHECKLIST ITEM FORM -->
		<div id="profile" class="hide-div section" style="display:none;margin-top:-30px;background-color:#F6F6F6;padding:10px;width:90%;margin:15px;">
				<h3>Add New Checlist Item</h3>
				<div class="leftsec">
					<p>
						<label>Name: </label>
						<input class="text" id="add_item" name="add_item[]" type="text" value="" />
					</p>
				</div>
				<div class="rightsec">
					<p>
						<label>Link (Optional): </label>
						<input class="text" name="add_item[]" type="text" value="" />
					</p>
				</div>
				<div class="clear"></div>
				<p id="submit_line" >
					<span>
						<input class="submit" type="submit" value="Submit" />
					</span> &nbsp;&nbsp;<?php i18n('OR'); ?>&nbsp;&nbsp; 
					<a class="cancel" href="#"><?php i18n('CANCEL'); ?></a>
				</p>
		</div>
		<!-- END ADD NEW CHECKLIST ITEM FORM -->

	<ul class="seo_social_list">
	<?php
	$keyphrase_file = getXML(SEOFile); 
	$count = 0;
	foreach($keyphrase_file->CHECKLIST->LISTITEM as $key) 
	{
		$count++;
		$item_atts = $key->attributes();
		if($key->STATUS == 'on')
		{
			$checked = 'checked';
		}
		else
		{
			$checked = '';
		}
		if($key->LINK != '')
		{
			$link = 'href="'.$key->LINK.'"';
			$item_name = '<a href="'.$key->LINK.'" target="_blank">'.$key->NAME.'</a>';
		}
		else
		{
			$item_name = '<strong>'.$key->NAME.'</strong>';
		}
		?>
		<li>
			<input type="hidden" name="<?php echo $key->NAME; ?>[]" value="<?php echo $key->NAME; ?>" />
			<input type="hidden" name="<?php echo $key->NAME; ?>[]" value="<?php echo $key->LINK; ?>" />
			<input type="checkbox" name="<?php echo $key->NAME; ?>[]" <?php echo $checked; ?> />
				<?php echo $item_name; ?> &nbsp;&nbsp;&nbsp; <a href="load.php?id=website_seo&seo_bookmarking&delete_item=<?php echo $item_atts['id']; ?>" title="Delete <?php echo $key->NAME; ?>?">X</a>
		</li>
		<?php
	}
	?>
	</ul>
	<p><input type="submit" class="submit" value="submit" /></p>
	</form>
	<script type="text/javascript">
		
		/*** Show add-user form ***/
		$("#add-keyphrase").click(function () {
			$(".hide-div").show();
			$("#add-keyphrase").hide();
		});
		
		/*** Hide user form ***/
		$(".cancel").click(function () {
			$(".hide-div").hide();
			$("#add-keyphrase").show();
		});
	</script>
	</div>
<?php
}

function showNotes()
{	
	$Submit_SEO = new GetSEOdata;

	if(isset($_POST['NOTES']))
	{
		$array = array('NOTE'=> $_POST['notes']);
		$Submit_SEO->ProcessSEO($array, 'NOTES');
	}
	?>
	<div id="seo_notes">
		<h3>SEO Notes</h3>
		<form class="largeform" action="load.php?id=website_seo&seo_reports" method="post" accept-charset="utf-8">
		<p>
			<label>Notes</label>
			<textarea class="seo_notes" name="notes"><?php echo $Submit_SEO->SEOdata('NOTES','NOTE'); ?></textarea>
		</p>
	</div>
	<input type="hidden" name="NOTES" value="yes" />
	<p><input type="submit" class="submit" value="submit" /></p>
	</form>
	<?php		
}

function showFooter()
{	
	global $EDLANG, $EDOPTIONS, $toolbar, $EDTOOL, $SITEURL;
	$Submit_SEO = new GetSEOdata;

	if(isset($_POST['FOOTERS']))
	{
		$array = array('FOOTER'=> $_POST['FOOTER']);
		$Submit_SEO->ProcessSEO($array, 'FOOTERS');
	}
	?>
	<div id="seo_footer">
		<h3>Homepage Footer</h3>
		<p>
			This content will be displayed in your homepage templates footer.<br/>
			It requires <?php highlight_string('<?php get_footer(); ?>'); ?> to be in your template file.
		</p>
		<form class="largeform" action="load.php?id=website_seo&seo_footer" method="post" accept-charset="utf-8">
		<p>
			<textarea class="seo_footer" id="post-content" name="FOOTER"><?php echo $Submit_SEO->SEOdata('FOOTERS', 'FOOTER'); ?></textarea>
		</p>
	</div>
	<script type="text/javascript" src="template/js/ckeditor/ckeditor.js"></script>
	<script type="text/javascript">
	  // missing border around text area, too much padding on left side, ...
	  $(function() {
	    CKEDITOR.replace( 'post-content', {
		        skin : 'getsimple',
		        forcePasteAsPlainText : false,
		        language : '<?php echo $EDLANG; ?>',
		        defaultLanguage : '<?php echo $EDLANG; ?>',
		        entities : true,
		        uiColor : '#FFFFFF',
				height: '500px',
				baseHref : '<?php echo $SITEURL; ?>',
		        toolbar : [ <?php echo $toolbar; ?> ]
				    <?php echo $EDOPTIONS; ?>
	    })
	  });
	</script>
	<input type="hidden" name="FOOTERS" value="yes" />
	<p><input type="submit" class="submit" value="submit" /></p>
	</form>
	<?php		
}

function showReports()
{	
	$Submit_SEO = new GetSEOdata;
	?>
	<div id="seo_reports" style="">
	<h3 class="floated">SEO Reports</h3>
	<div class="edit-nav clearfix" style="">
		<a href="#" class="upload_report">Upload New Report</a>
	</div>
	<div id="upload_report" style="display:none;padding:10px;background-color:#f6f6f6;margin:10px;">
		<form enctype="multipart/form-data" method="post" action="load.php?id=website_seo&seo_reports">
			<p>
				<label>Upload Report: </label>
				<input type="file" name="upload_report" id="file" style="float:left;"><input type="submit" name="upload_report" value="Upload Report" style="float:left;margin-left:20px;padding:2px;">
			</p>
		</form>
	</div>
		<ul>
			<?php
			global $SITEURL;
			if(file_exists(SEOPath))
			{
				$i = 0;
				foreach (glob(SEOPath."*") as $filename) 
				{
						$i++;
					echo '<li><a href="'.$SITEURL.'/data/seo/'.basename($filename).'">'.basename($filename).'</a></li>';
				}
				if($i == 0)
				{
					echo '<li>No Reports Found</li>';
				}
			}
			?>
		</ul>
	</div>
	<script type="text/javascript">
		$(document).ready(function() {
			$('.upload_report').click(function() {
				$('#upload_report').show();
				$('.upload_report').hide();
			})
		})
	</script>
	<?php		
}

function showNotices()
{
	?>
	<h3>Notices</h3>

	<h4>Missing MetaTags</h4>
	<p>Below are pages on your website that do not have a meta description or meta keywords.</p>
	<ul>
		<?php
		$dir = GSDATAPAGESPATH;
		foreach (glob($dir."*.xml") as $file) 
		{
			$xml = getXML($file);
			if(empty($xml->meta) || empty($xml->metad))
			{
				if(empty($xml->meta) && empty($xml->metad))
				{
					echo '<li><a href="edit.php?id='.$xml->url.'" target="_blank">'.$xml->title.'</a> &nbsp;- <strong>Missing Both Meta Description & Meta Keywords</strong></li>';
				}
				elseif(empty($xml->meta))
				{
					echo '<li><a href="edit.php?id='.$xml->url.'" target="_blank">'.$xml->title.'</a> &nbsp;- <strong>Missing Meta Keywords</strong></li>';
				}
				elseif(empty($xml->metad))
				{
					echo '<li><a href="edit.php?id='.$xml->url.'" target="_blank">'.$xml->title.'</a> &nbsp;- <strong>Missing Meta Description</strong></li>';
				}
			}
		}
		?>

	</ul>
	<?php
}

function return_keyphrase($keyphraseN)
{
	$keyphrase_data = new GetSEOdata;
	return $keyphrase_data->getKeyphrase($keyphraseN);
	
}

function get_keyphrase($keyphraseN)
{
	$keyphrase_data = new GetSEOdata;
	echo $keyphrase_data->getKeyphrase($keyphraseN);
	
}


function get_seo($data)
{
	$siteinfo_data = new GetSEOdata;
	echo $siteinfo_data->SEOdata('SITEINFO', $data);
	
}

function return_footer()
{
	if(!isset($_GET['id']) || $_GET['id'] == 'index')
	{
		$footer_data = new GetSEOdata;
		echo '<center>'.$footer_data->SEOdata('FOOTERS','FOOTER').'</center>';
	}
}

global $EDLANG, $EDOPTIONS, $toolbar, $EDTOOL;
if (defined('GSEDITORLANG')) { $EDLANG = GSEDITORLANG; } else {	$EDLANG = 'en'; }
if (defined('GSEDITORTOOL')) { $EDTOOL = GSEDITORTOOL; } else {	$EDTOOL = 'basic'; }
if (defined('GSEDITOROPTIONS') && trim(GSEDITOROPTIONS)!="") { $EDOPTIONS = ", ".GSEDITOROPTIONS; } else {	$EDOPTIONS = ''; }
if ($EDTOOL == 'advanced') {
$toolbar = "
	    ['Bold', 'Italic', 'Underline', 'NumberedList', 'BulletedList', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', 'Table', 'TextColor', 'BGColor', 'Link', 'Unlink', 'Image', 'RemoveFormat', 'Source'],
    '/',
    ['Styles','Format','Font','FontSize']
";
} elseif ($EDTOOL == 'basic') {
$toolbar = "['Bold', 'Italic', 'Underline', 'NumberedList', 'BulletedList', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', 'Link', 'Unlink', 'Image', 'RemoveFormat', 'Source']";
} else {
	$toolbar = GSEDITORTOOL;
}

function to7bits($text,$from_enc="UTF-8") 
{
	if (function_exists('mb_convert_encoding')) 
	{
		$text = mb_convert_encoding($text,'HTML-ENTITIES',$from_enc);
	}
	$text = preg_replace(
	array('/&szlig;/','/&(..)lig;/','/&([aouAOU])uml;/','/&(.)[^;]*;/'),array('ss',"$1","$1".'e',"$1"),$text);
	$text = strtolower($text);
	$text = str_replace(' ', '-',$text);
	return $text;
}

?>