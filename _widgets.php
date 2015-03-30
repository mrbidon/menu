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
if (!defined('DC_RC_PATH')) { return; }

$core->addBehavior('initWidgets',array('menuWidgets','initWidgets'));

class menuWidgets
{
	public static function initWidgets($w)
	{
		$w->create('menu',__('Menu'),array('tplMenu','menuWidget'),
			null,
			__('List of menu items at one or more levels'));
		$w->menu->setting('title',__('Title:'),__('Menu'));
		$w->menu->setting('homeonly',__('Display on:'),0,'combo',
			array(
				__('All pages') => 0,
				__('Home page only') => 1,
				__('Except on home page') => 2
				)
		);
		$w->menu->setting('content_only',__('Content only'),0,'check');
		$w->menu->setting('class',__('CSS class:'),'');
		$w->menu->setting('offline',__('Offline'),0,'check');
	}
}