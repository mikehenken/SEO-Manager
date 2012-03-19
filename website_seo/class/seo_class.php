<?php
class GetSEOdata 
{
	public function __construct()
	{
		if (!file_exists(SEOPath)) 
		{
			mkdir(GSDATAPATH.'seo', 0755);
			$ourFileName = GSDATAPATH.'seo/.htaccess';
			$ourFileHandle = fopen($ourFileName, 'w') or die("can't open file");
			$stringData = "Allow from all";
			fwrite($ourFileHandle, $stringData);
			fclose($ourFileHandle);
			if (!file_exists(ITEMDATA)) 
			{
				echo '<h3>'.IMTITLE.' Manager</h3><p>The directory "<i>'.GSDATAPATH.'seo</i>"
				does not exist. It is required for this plugin to function properly.
				Please create it manually and make sure it is writable.</p>';
			}
			else
			{
				echo '<div class="updated"><strong>The below directory has been succesfully created:</strong><br/>"'.SEOPath.'"</div>';
			}
		}
		if(!file_exists(SEOFile))
		{
			$xml = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><item>
	<KEYPHRASES>
		<KEYPHRASE><![CDATA[Keyphrase 1]]></KEYPHRASE>
		<KEYPHRASE><![CDATA[Keyphrase 2]]></KEYPHRASE>
	</KEYPHRASES>
	<SITEINFO>
		<TITLE><![CDATA[SEO Title]]></TITLE>
		<SDESC><![CDATA[]]></SDESC>
		<LDESC><![CDATA[]]></LDESC>
		<ARTICLES><![CDATA[]]></ARTICLES>
	</SITEINFO>
	<NOTES>
		<NOTE><![CDATA[notes2]]></NOTE>
	</NOTES>
	<FOOTERS>
		<FOOTER><![CDATA[<p>
	Homepage Footer SEO Content: Many websites choose to include keyphrase rich content in their homepages footer to increase rankings and content relevancy.&nbsp;</p>
]]></FOOTER>
</FOOTERS>
<CHECKLIST>
	<LISTITEM id="google-webmasters"><NAME><![CDATA[Google Webmasters]]></NAME><LINK><![CDATA[http://www.google.com/webmasters/]]></LINK><STATUS><![CDATA[]]></STATUS></LISTITEM>
	<LISTITEM id="digg"><NAME><![CDATA[Digg]]></NAME><LINK><![CDATA[http://digg.com/submit]]></LINK><STATUS><![CDATA[]]></STATUS></LISTITEM>
	<LISTITEM id="delicious"><NAME><![CDATA[Delicious]]></NAME><LINK><![CDATA[http://del.icio.us/post]]></LINK><STATUS><![CDATA[]]></STATUS></LISTITEM>
	<LISTITEM id="stumbleupon"><NAME><![CDATA[StumbleUpon]]></NAME><LINK><![CDATA[http://www.stumbleupon.com/submit]]></LINK><STATUS><![CDATA[]]></STATUS></LISTITEM>
	<LISTITEM id="reddit"><NAME><![CDATA[Reddit]]></NAME><LINK><![CDATA[http://www.reddit.com/submit]]></LINK><STATUS><![CDATA[]]></STATUS></LISTITEM>
	<LISTITEM id="tagza"><NAME><![CDATA[Tagza]]></NAME><LINK><![CDATA[http://tagza.com/submit]]></LINK><STATUS><![CDATA[]]></STATUS></LISTITEM>
	<LISTITEM id="bibsonomy"><NAME><![CDATA[Bibsonomy]]></NAME><LINK><![CDATA[http://www.bibsonomy.org/ShowBookmarkEntry?c=b&jump=yes]]></LINK><STATUS><![CDATA[]]></STATUS></LISTITEM>
	<LISTITEM id="social-bookmarking.net"><NAME><![CDATA[Social-Bookmarking.net]]></NAME><LINK><![CDATA[http://www.social-bookmarking.net/submit]]></LINK><STATUS><![CDATA[]]></STATUS></LISTITEM>
</CHECKLIST>
</item>');
			if(XMLsave($xml, SEOFile))
			{
				echo '<div class="updated">SEO File Succesfully Written</div>';
			}
		}
	}
	
	
	public function getKeyphrase($get_keyphrase)
	{
		$keyphrase_file = getXML(SEOFile);
		$keyphrase_return = '';
		$count = 0;
		if(is_int($get_keyphrase))
		{
			foreach($keyphrase_file->KEYPHRASES->KEYPHRASE as $keyphrase)
			{
				$count++;
				if($get_keyphrase == $count)
				{
					$keyphrase_return = $keyphrase;
				}
			}
			return $keyphrase_return;
		}
	}

	public function SEOdata($parent=null, $get_keyphrase)
	{
		$keyphrase_file = getXML(SEOFile);
		return $keyphrase_file->$parent->$get_keyphrase;
	}
	
	public function checkCheckBoxes($checkbox_type,$checkbox_name)
	{
		$keyphrase_file = getXML(SEOFile);
		if($keyphrase_file->$checkbox_type->$checkbox_name == 'on')
		{
			return 'checked';
		}
		else
		{
			return '';
		}
	}
	
	public function ProcessSEO($data=null, $type, $delete=null)
	{
		$parents = array('KEYPHRASES','SITEINFO','NOTES','FOOTERS','CHECKLIST');
		$seo_file = getXML(SEOFile);
	
		$xml = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><item></item>');
		
		foreach($parents as $parent)
		{
			if($parent == $type)
			{
				if($parent == 'KEYPHRASES' && $data != null)
				{
					$parent_nodes = $xml->addChild($parent);
					$count = 0;
					foreach($data as $value)
					{
						$count++;
						$parent_nodes_node = $parent_nodes->addChild('KEYPHRASE');
						$parent_nodes_node->addAttribute('id', $count);
						$parent_nodes_node->addCData($value); 
					}
				}
				elseif($parent == 'KEYPHRASES' && $data == null)
				{
					$parent_nodes = $xml->addChild($parent);
					$count = 0;
					foreach($seo_file->KEYPHRASES->KEYPHRASE as $value)
					{
						$count++;
						if($delete == $count)
						{

						}
						else
						{
							$parent_nodes_node = $parent_nodes->addChild('KEYPHRASE');
							$parent_nodes_node->addAttribute('id', $count);
							$parent_nodes_node->addCData($value); 
						}
					}
				}
				elseif($parent == 'CHECKLIST' && $data != null)
				{
					$parent_nodes = $xml->addChild('CHECKLIST');
					foreach($data as $value)
					{
						$checklist_id = to7bits($value['NAME'], "UTF-8");

						$list_item = $parent_nodes->addChild('LISTITEM');
						$list_item->addAttribute('id', $checklist_id);

						$list_item_name = $list_item->addChild('NAME');
						$list_item_name->addCData($value['NAME']);

						$list_item_link = $list_item->addChild('LINK');
						$list_item_link->addCData($value['LINK']); 

						$list_item_status = $list_item->addChild('STATUS');
						$list_item_status->addCData($value['STATUS']); 

					}
				}
				elseif($parent == 'CHECKLIST' && $data == null)
				{
					$parent_nodes = $xml->addChild('CHECKLIST');
					foreach($seo_file->CHECKLIST->LISTITEM as $value)
					{
						$item_atts = $value->attributes();

						if($delete == $item_atts['id'])
						{

						}
						else
						{
							$list_item = $parent_nodes->addChild('LISTITEM');
							$list_item->addAttribute('id', $item_atts['id']);

							$list_item_name = $list_item->addChild('NAME');
							$list_item_name->addCData($value->NAME);

							$list_item_link = $list_item->addChild('LINK');
							$list_item_link->addCData($value->LINK); 

							$list_item_status = $list_item->addChild('STATUS');
							$list_item_status->addCData($value->STATUS); 
						}
					}
				}
				else
				{
					$parent_nodes = $xml->addChild($parent);
					foreach($data as $key => $value)
					{
						$parent_nodes_node = $parent_nodes->addChild($key);
							$parent_nodes_node->addCData($value); 
					}
				}
			}	
			else
			{
				if($parent != 'CHECKLIST')
				{
					foreach($seo_file->$parent as $parent_node)
					{
						$parent_nodes = $xml->addChild($parent);
						foreach($parent_node as $key => $value)
						{
							$parent_nodes_node = $parent_nodes->addChild($key);
								$parent_nodes_node->addCData($value); 
						}	
					}
				}	
				else
				{
					$parent_nodes = $xml->addChild($parent);
					foreach($seo_file->CHECKLIST->LISTITEM as $value)
					{
						$item_atts = $value->attributes();

						$list_item = $parent_nodes->addChild('LISTITEM');
						$list_item->addAttribute('id', $item_atts['id']);

						$list_item_name = $list_item->addChild('NAME');
						$list_item_name->addCData($value->NAME);

						$list_item_link = $list_item->addChild('LINK');
						$list_item_link->addCData($value->LINK); 

						$list_item_status = $list_item->addChild('STATUS');
						$list_item_status->addCData($value->STATUS); 
					}
				}
			}
		}

		//Save XML File
		if(XMLsave($xml, SEOFile))
		{
			echo '<div class="updated">File Succesfully Written</div>';
		}
			
	}

	public function uploadReport()
	{
		if(isset($_FILES['upload_report']) && !empty($_FILES['upload_report']['name'])) 
		{
			//echo "below is path.filename".'<br/>';
			$filename = $_FILES['upload_report']['name'];
			$full_old_path = SEOPath.$filename;
			if(file_exists($full_old_path))
			{
				$backup_path = SEOPath;
				$date = date("m-d-y");
				$new_file_name = str_replace(".zip","--$date.zip",$filename);
				//echo $new_file_name.'<br/>';
				if(file_exists($backup_path.$new_file_name))
				{
					$randomn = rand(1,20);
					$new_file_name = str_replace(".zip","--$date--$randomn.zip",$filename);
				}
				rename($full_old_path, $backup_path.$new_file_name);
			}
			
			$target_path = SEOPath . basename( $_FILES['upload_report']['name']); 
			if(move_uploaded_file($_FILES['upload_report']['tmp_name'], $target_path)) 
			{
				/*echo "The file ".  basename( $_FILES['upload_report']['name']). 
				" has been uploaded";
				*/
				$the_filename = basename( $_FILES['upload_report']['name']);
			} 
			else
			{
				echo "There was an error uploading the file, please try again!";
				$the_filename = 'error';
			}
		}
		else
		{
			$target_path = SEOPath . basename( $_FILES['upload_report']['name']); 
			if(move_uploaded_file($_FILES['upload_report']['tmp_name'], $target_path)) 
			{
				echo '<div class="updated">'.basename( $_FILES['upload_report']['name']).' has been succesfully uploaded';
				$the_filename = basename( $_FILES['upload_report']['name']);
			} 
			else
			{
				echo "There was an error uploading the file, please try again!";
				$the_filename = 'error';
			}
		}
	}
}

?>