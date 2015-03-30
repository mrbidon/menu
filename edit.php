<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
# This file is part of Menu, a plugin for Dotclear 2.
#
# Copyright (c) 2009-2015 Benoît Grelier and contributors
# Licensed under the GPL version 2.0 license.
# See LICENSE file or
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
#
# -- END LICENSE BLOCK ------------------------------------
if (!defined('DC_CONTEXT_ADMIN')) { return; }
$page_title = __('Menu');

# Url de base
$p_url = 'plugin.php?p=menu';

$id = $_REQUEST['id'];

try {
	$rs = $menu->getLink($id);
} catch (Exception $e) {
	$core->error->add($e->getMessage());
}

if (!$core->error->flag() && $rs->isEmpty()) {
	$core->error->add(__('No such link or title'));
} else {
	$link_title = $rs->link_title;
	$link_href = $rs->link_href;
	$link_level = $rs->link_level;
	$link_desc = $rs->link_desc;
	$link_lang = $rs->link_lang;
	$link_class = $rs->link_class;

	# auto link
	$link_auto = 0;
	//$link_auto = $rs->link_auto;
	
	//$links_auto_combo = array(
	//	'-' => '0',
	//__('categories') => '1',
	//__('tags') => '2',
	//__('posts selected') => '3'
	//);
}


# Update a link
if (isset($rs) && !empty($_POST['edit_link']))
{
	$link_title = $_POST['link_title'];
	$link_href = $_POST['link_href'];
	$link_level = $_POST['link_level'];
	$link_desc = $_POST['link_desc'];
	$link_lang = $_POST['link_lang'];
	$link_class = $_POST['link_class'];
	# auto link
	$link_auto = 0;
	//$link_auto = $_POST['link_auto'];
	
	try {
		$menu->updateLink($id,$link_title,$link_href,$link_level,$link_auto,$link_desc,$link_lang,$link_class);
		http::redirect($p_url.'&edit=1&id='.$id.'&upd=1');
	} catch (Exception $e) {
		$core->error->add($e->getMessage());
	}
}

?>
<html>
<head>
<title><?php echo $page_title; ?></title>
</head>

<body>
<?php
	echo dcPage::breadcrumb(
		array(
			html::escapeHTML($core->blog->name) => '',
			$page_title => $p_url,
			__('Changing the item') => ''
		));
		
if (isset($rs)) {
if (!empty($_GET['upd'])) {
  dcPage::success(__('Items has been successfully updated'));
}

echo '<p><a class="back" href="'.$p_url.'">'.__('Return to menu').'</a></p>';

	echo
	'<form action="plugin.php" method="post">'.
	'<div class="fieldset two-cols"><h4>'.__('Changing the item').'</h4>'.
	
	'<p class="field"><label class="classic required" for="link_title"><abbr title="'.__('Required field').'">*</abbr> '.__('Label of item menu:').' </label>'.
	form::field('link_title',50,255,html::escapeHTML($link_title)).'</p>'.

	'<p class="field"><label class="classic required" for="link_href"><abbr title="'.__('Required field').'">*</abbr> '.__('URL of item menu:').' </label>'.
	form::field('link_href',50,255,html::escapeHTML($link_href)).'</p>'.
	
	'<p class="field"><label class="classic" for="link_desc">'.__('Description:').' </label>'.
	form::field('link_desc',50,255,html::escapeHTML($link_desc)).'</p>'.

	'<p class="field"><label class="classic" for="link_level">'.__('Level:').' </label>'.
	form::field('link_level',5,255,html::escapeHTML($link_level)).'</p>'.
'<p class="info">'.__('Note: 0 = hide menu item; 1 = level of item 1; 2 = item level 2; etc.').'</p>'.
	
	'<p class="field"><label class="classic" for="link_lang">'.__('Language:').' </label>'.
	form::field('link_lang',5,5,html::escapeHTML($link_lang)).'</p>'.

	'<p class="field"><label class="classic" for="link_class">'.__('Class:').' </label>'.
	form::field('link_class',50,32,html::escapeHTML($link_class)).'</p>'.
	
	# Auto links
	//'<p class="col"><label>'.__('Auto links:').' '.
	//form::combo('link_auto',$links_auto_combo,$link_auto).'</label></p>'.
	'</div>'.
	
	'<p>'.form::hidden('p','menu').
	form::hidden('edit',1).
	form::hidden('id',$id).
	$core->formNonce().
	'<input type="submit" name="edit_link" class="submit" value="'.__('Save').'"/></p>'.

	'</form>';
}
dcPage::helpBlock('menu');
?>
</body>
</html>