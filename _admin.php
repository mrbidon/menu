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

$core->addBehavior('adminDashboardFavorites','menuDashboardFavorites');

function menuDashboardFavorites($core,$favs)
{
	$favs->register('menu', array(
		'title' => __('Menu'),
		'url' => 'plugin.php?p=menu',
		'small-icon' => 'index.php?pf=menu/icon.png',
		'large-icon' => 'index.php?pf=menu/icon-big.png',
		'permissions' => 'usage,contentadmin'
	));
}

$_menu['Blog']->addItem(__('Menu'),'plugin.php?p=menu','index.php?pf=menu/icon.png',
                preg_match('/plugin.php\?p=menu(&.*)?$/',$_SERVER['REQUEST_URI']),
                $core->auth->check('usage,contentadmin',$core->blog->id));

$core->auth->setPermissionType('menu',__('manage menu'));

require dirname(__FILE__).'/_widgets.php';

# Import-export :
/*
$core->addBehavior('exportFull',array('menuBehaviors','exportFull'));
$core->addBehavior('exportSingle',array('menuBehaviors','exportSingle'));
$core->addBehavior('importInit',array('menuBehaviors','importInit'));
$core->addBehavior('importSingle',array('menuBehaviors','importSingle'));
$core->addBehavior('importFull',array('menuBehaviors','importFull'));

class menuBehaviors
{
	public static function exportFull($core,$exp)
	{
		$exp->exportTable('menu');
	}

	public static function exportSingle($core,$exp,$blog_id)
	{
		$exp->export('menu',
			'SELECT * FROM '.$this->core->prefix.'menu '.
			"WHERE blog_id = '".$blog_id."'"
		);
	}
	
	public static function importInit($bk,$core)
	{
		$bk->cur_menu = $core->con->openCursor($core->prefix.'menu');
		$bk->menu = new dcBlogMenu($core);	
	}
	
	public static function importFull($line,$bk,$core)
	{
	
	}
	
	public static function importSingle($line,$bk,$core)
	{
		if ($line->__name == menu) {
			$menu->blog_id = $bk->blog_id;

		}
	}
}
//*/