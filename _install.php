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

$version = $core->plugins->moduleInfo('menu','version');

if (version_compare($core->getVersion('menu'),$version,'>=')) {
	return;
}

/* Database schema
-------------------------------------------------------- */
$s = new dbStruct($core->con,$core->prefix);

$s->menu
	->link_id		('bigint',	0,		false)
	->blog_id		('varchar',	32,		false)
	->link_href		('varchar',	255,	false)
	->link_title	('varchar',	255,	false)
	->link_desc		('varchar',	255,	true)
	->link_lang		('varchar',	5,		true)
	->link_class 	('varchar',	32, 	true)
	->link_position	('integer',	0,	false, 0)
	->link_level	('smallint', 0,	false, 0)
	->link_auto		('smallint', 0,	false, 0)
	->link_limit	('smallint', 0,	false, 0)
	
	->primary('pk_menu','link_id')
	;

$s->menu->index('idx_menu_blog_id','btree','blog_id');
$s->menu->reference('fk_menu_blog','blog_id','blog','blog_id','cascade','cascade');

# Schema installation
$si = new dbStruct($core->con,$core->prefix);
$changes = $si->synchronize($s);

$core->setVersion('menu',$version);
return true;