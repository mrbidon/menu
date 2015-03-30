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

$this->registerModule(
	/* Name */			"Menu",
	/* Description*/		"Manage your menu",
	/* Author */		"Adjaya, Pierre Van Glabeke",
	/* Version */		'1.8.7',
	/* Properties */
	array(
		'permissions' => 'menu',
		'type' => 'plugin',
		'dc_min' => '2.7',
		'support' => 'http://forum.dotclear.org/viewtopic.php?id=32705',
		'details' => 'http://plugins.dotaddict.org/dc2/details/menu'
		)
);