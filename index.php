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

require dirname(__FILE__).'/class.dc.blogmenu.php';

$menu = new dcBlogMenu($core->blog);

$page_title = __('Menu');

if (!empty($_REQUEST['edit']) && !empty($_REQUEST['id'])) {
	include dirname(__FILE__).'/edit.php';
	return;
}

$default_tab = 'menu';
$link_title = $link_href = $link_desc = $link_lang = $link_class = '';
$link_level = $link_auto = 0;

# Add link
if (!empty($_POST['add_link']))
{
	# auto link
//	$links_auto_combo = array(
//		'-' => '0',
//	__('categories') => '1',
//	__('tags') => '2',
//	__('posts selected') => '3'
//	);

	$link_title = $_POST['link_title'];
	$link_level = $_POST['link_level'];
	//$link_auto = $_POST['link_auto'];
	$link_auto = 0;
	$link_href = $_POST['link_href'];
	$link_desc = $_POST['link_desc'];
	$link_lang = $_POST['link_lang'];
	$link_class = $_POST['link_class'];
	
	try {
		$menu->addLink($link_title,$link_href,$link_level,$link_auto,0,$link_desc,$link_lang,$link_class);
		http::redirect($p_url.'&addlink=1');
	} catch (Exception $e) {
		$core->error->add($e->getMessage());
		$default_tab = 'add-link';
	}
}

# Delete link
if (!empty($_POST['removeaction']) && !empty($_POST['remove'])) {
	foreach ($_POST['remove'] as $k => $v)
	{
		try {
			$menu->delItem($v);
		} catch (Exception $e) {
			$core->error->add($e->getMessage());
			break;
		}
	}
	
	if (!$core->error->flag()) {
		http::redirect($p_url.'&removed=1');
	}
}

# Order links
$order = array();
if (empty($_POST['links_order']) && !empty($_POST['order'])) {
	$order = $_POST['order'];
	asort($order);
	$order = array_keys($order);
} elseif (!empty($_POST['links_order'])) {
	$order = explode(',',$_POST['links_order']);
}

# Update items
if (!empty($_POST['updateitems']))
{
	if (!empty($order))
	{
		foreach ($order as $pos => $l) {
			$pos = ((integer) $pos)+1;
				
			try {
				$menu->updateOrder($l,$pos);
			} catch (Exception $e) {
				$core->error->add($e->getMessage());
			}
		}
	}

	if (!empty($_POST['levels']))
	{
		foreach ($_POST['levels'] as $k => $v)
		{
			try {
				$menu->updateLevel($k,$v);
			} catch (Exception $e) {
				$core->error->add($e->getMessage());
				break;
			}
		}
	}
	
	if (!$core->error->flag()) {
		http::redirect($p_url.'&newconfig=1');
	}
}

# Get links
try {
	$rs = $menu->getLinks();
} catch (Exception $e) {
	$core->error->add($e->getMessage());
}

?>
<html>
<head>
<title><?php echo $page_title; ?></title>
  <?php echo dcPage::jsToolMan(); ?>
  <?php echo dcPage::jsConfirmClose('links-form','add-link-form'); ?>
  <script type="text/javascript">
  //<![CDATA[
  
  var dragsort = ToolMan.dragsort();
  $(function() {
  	dragsort.makeTableSortable($("#links-list").get(0),
  	dotclear.sortable.setHandle,dotclear.sortable.saveOrder);
	
	$('.checkboxes-helpers').each(function() {
		dotclear.checkboxesHelpers(this);
	});
  });
  
  dotclear.sortable = {
	  setHandle: function(item) {
		var handle = $(item).find('td.handle').get(0);
		while (handle.firstChild) {
			handle.removeChild(handle.firstChild);
		}
		
		item.toolManDragGroup.setHandle(handle);
		handle.className = handle.className+' handler';
	  },
	  
	  saveOrder: function(item) {
		var group = item.toolManDragGroup;
		var order = document.getElementById('links_order');
		group.register('dragend', function() {
			order.value = '';
			items = item.parentNode.getElementsByTagName('tr');
			
			for (var i=0; i<items.length; i++) {
				order.value += items[i].id.substr(2)+',';
			}
		});
	  }
  };
  //]]>
  </script>
  <?php echo dcPage::jsPageTabs($default_tab); ?>
</head>

<body>
<?php
	echo dcPage::breadcrumb(
		array(
			html::escapeHTML($core->blog->name) => '',
			'<span class="page-title">'.$page_title.'</span>' => ''
		));
if (!empty($_GET['newconfig'])) {
  dcPage::success(__('Items has been successfully updated'));
}
if (!empty($_GET['removed'])) {
  dcPage::success(__('Items have been successfully removed.'));
}
if (!empty($_GET['addlink'])) {
  dcPage::success(__('Menu item has been successfully created.'));
}
?>

<div class="multi-part" title="<?php echo __('Menu'); ?>" id="menu">
<form action="plugin.php" method="post" id="links-form">
<table class="maximal dragable">
<thead>
<tr>
  <th><?php echo __('Order'); ?></th>
  <th><?php echo __('Level'); ?></th>
  <th colspan="2"><?php echo __('Label'); ?></th>
  <th><?php echo __('Description'); ?></th>
  <!-- <th><?php //echo __('Auto'); ?></th> -->
  <th><?php echo __('URL'); ?></th>
  <th><?php echo __('Lang'); ?></th>
  <th><?php echo __('Class'); ?></th>
</tr>
</thead>
<tbody id="links-list">
<?php
# link auto
//$links_auto_combo = array(
//	'-' => '0',
//__('categories') => '1',
//__('tags') => '2',
//__('posts selected') => '3'
//);

while ($rs->fetch())
{
	$position = (string) $rs->index()+1;

	if ($rs->link_class == '') {
		$class = 'menu-item-'.$rs->link_id; //default
	} else {
		$class = $rs->link_class;
	}
	# link auto
	//$link_auto = (integer) $rs->link_auto;

	//foreach ($links_auto_combo as $k => $v)
	//{
	//	if ($v == $link_auto) {
	//		$link_auto = $k;
	//	}
	//}
	
	echo
	'<tr class="line" id="l_'.$rs->link_id.'">'.
	'<td class="handle minimal">'.form::field(array('order['.$rs->link_id.']'),2,5,$position).'</td>'.
	'<td class="nowrap" scope="row">'.
	form::field(array('levels['.$rs->link_id.']'),2,5,html::escapeHTML($rs->link_level)).'</td>'.
	'<td class="minimal">'.form::checkbox(array('remove[]'),$rs->link_id).'</td>';

		echo
		'<td><a href="'.$p_url.'&amp;edit=1&amp;id='.$rs->link_id.'">'.
		html::escapeHTML($rs->link_title).'</a></td>'.
		
		//'<td>'.html::escapeHTML($link_auto).'</td>'.
		'<td>'.html::escapeHTML($rs->link_desc).'</td>'.
		'<td>'.html::escapeHTML($rs->link_href).'</td>'.
		'<td>'.html::escapeHTML($rs->link_lang).'</td>'.
		'<td>'.html::escapeHTML($class).'</td>';
	
	echo '</tr>';
}
?>
</tbody>
</table>

<div class="two-cols">
<p class="col"><?php echo form::hidden('links_order','');
echo form::hidden('links_levels','');
echo form::hidden(array('p'),'menu');
echo $core->formNonce(); ?>
<input type="submit" name="updateitems" value="<?php echo __('Update menu'); ?>" /></p>

<p class="col right"><input class="delete" type="submit" name="removeaction"
value="<?php echo __('Delete selected menu items'); ?>"
onclick="return window.confirm('<?php echo html::escapeJS(
__('Are you sure you want to remove selected menu items?')); ?>');" /></p>
</div>

</form>
</div>

<?php
echo
'<div class="multi-part" id="add-link" title="'.__('Add an item').'">'.
'<form action="plugin.php" method="post" id="add-link-form">'.
'<div class="fieldset two-cols"><h4>'.__('New item').'</h4>'.
'<p class="field"><label class="classic required" for="link_title"><abbr title="'.__('Required field').'">*</abbr> '.__('Label of item menu:').' </label>'.
form::field('link_title',50,255,$link_title,'',1).
'</p>'.

'<p class="field"><label class="classic required" for="link_href"><abbr title="'.__('Required field').'">*</abbr> '.__('URL of item menu:').' </label>'.
form::field('link_href',50,255,$link_href,'',2).
'</p>'.

'<p class="field"><label class="classic" for="link_desc">'.__('Description:').' </label>'.
form::field('link_desc',50,255,$link_desc,'',3).
'</p>'.

'<p class="field"><label class="classic" for="link_level">'.__('Level:').' </label>'.
form::field('link_level',5,255,$link_level,'',5).
'</p>'.
'<p class="info">'.__('Note: 0 = hide menu item; 1 = level of item 1; 2 = item level 2; etc.').'</p>'.

'<p class="field"><label class="classic" for="link_lang">'.__('Language:').' </label>'.
form::field('link_lang',5,5,$link_lang,'',4).
'</p>'.

'<p class="field"><label class="classic" for="link_class">'.__('Class:').' </label>'.
form::field('link_class',50,32,$link_class,'',6).
'</p>'.

# Auto link
//'<p class="col"><label>'.__('Auto links:').' '.
//form::combo('link_auto',$links_auto_combo,$link_auto,'',6).
//'</label></p>'.
'</div>'.

'<p>'.form::hidden(array('p'),'menu').
$core->formNonce().
'<input type="submit" name="add_link" value="'.__('Save').'" tabindex="7" /></p>'.
'</form>'.
'</div>';
dcPage::helpBlock('menu');
?>
</body>
</html>