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

class dcBlogMenu
{
	private $blog;
	private $con;
	private $table;
	
	public function __construct($blog)
	{
		$this->blog =& $blog;
		$this->con =& $blog->con;
		$this->table = $this->blog->prefix.'menu';
	}

	# auto link
	public function linkAutoCombo($links_auto_combo=array())
	{
		$links_auto_combo = array(
			'-' => '0',
		__('categories') => '1',
		__('tags') => '2',
		__('posts selected') => '3'
		);
		
		return $links_auto_combo;
	}
	
	public function getLinks($params=array())
	{
		$desc = '';

		$strReq = 'SELECT link_id, link_title, link_desc, link_href, '.
				'link_lang, link_class, link_position, '.
				'link_level, link_auto '.
				'FROM '.$this->table.' '.
				"WHERE blog_id = '".$this->con->escape($this->blog->id)."' ";
		
		if (isset($params['link_id'])) {
			$strReq .= 'AND link_id = '.(integer) $params['link_id'].' ';
		}

		if (isset($params['desc'])) {
			$desc = 'DESC ';
		}

		if (isset($params['level']) && $params['level'] > 0) {
			$params['link_level'] = $params['level'];
			$strReq .= 'AND link_level <= '.(integer) $params['link_level'].' ';
		}

		if (!$GLOBALS['core']->auth->userID()) {
			$link_out = 0;	
			$strReq .= 'AND link_level > '.(integer) $link_out.' ';
		}

		$strReq .= 'ORDER BY link_position '.$desc;

		$rs = $this->con->select($strReq);
		//$rs = $rs->toStatic();

		return $rs;
	}

	#
	public function getLinksLevels($params=array())
	{
		$strReq = 'SELECT link_level '.
				'FROM '.$this->table.' '.
				"WHERE blog_id = '".$this->con->escape($this->blog->id)."' ";

		if (isset($params['link_level'])) {
			$strReq .= 'AND link_level = '.(integer) $params['link_level'].' ';
		}

		$strReq .= 'ORDER BY link_position ';

		$rs = $this->con->select($strReq);
		//$rs = $rs->toStatic();

		return $rs;
	}

	public function getLink($id)
	{
		$params['link_id'] = $id;
		
		$rs = $this->getLinks($params);
		
		return $rs;
	}
	
	public function addLink($title,$href,$level,$auto,$limit,$desc='',$lang='',$class='')
	{
		$cur = $this->con->openCursor($this->table);
		
		$cur->blog_id = (string) $this->blog->id;
		$cur->link_title = (string) $title;
		$cur->link_href = (string) $href;
		$cur->link_level = (integer) $level;
		$cur->link_auto = (integer) $auto;
		$cur->link_limit = (integer) $limit;
		$cur->link_desc = (string) $desc;
		$cur->link_lang = (string) $lang;
		$cur->link_class = (string) $class;

		
		if ($cur->link_title == '') {
			throw new Exception(__('Label and URL of menu item are mandatory.'));
		}
		
		if ($cur->link_href == '') {
			throw new Exception(__('Label and URL of menu item are mandatory.'));
		}
		
		$strReq = 'SELECT MAX(link_id), MAX(link_position) FROM '.$this->table;
		$rs = $this->con->select($strReq);
		$cur->link_id = (integer) $rs->f(0) + 1;
		$cur->link_position = (integer) $rs->f(0) + 1;
		
		$cur->insert();
		$this->blog->triggerBlog();
	}
	
	public function updateLink($id,$title,$href,$level,$auto,$desc='',$lang='',$class='')
	{
		$cur = $this->con->openCursor($this->table);
		
		$cur->link_title = (string) $title;
		$cur->link_href = (string) $href;
		$cur->link_level = (integer) $level;
		$cur->link_auto = (integer) $auto;
		$cur->link_desc = (string) $desc;
		$cur->link_lang = (string) $lang;
		$cur->link_class = (string) $class;

		
		if ($cur->link_title == '') {
			throw new Exception(__('Label and URL of menu item are mandatory.'));
		}
		
		if ($cur->link_href == '') {
			throw new Exception(__('Label and URL of menu item are mandatory.'));
		}
		
		$cur->update('WHERE link_id = '.(integer) $id.
			" AND blog_id = '".$this->con->escape($this->blog->id)."'");
		$this->blog->triggerBlog();
	}
	
	public function delItem($id)
	{
		$id = (integer) $id;
		
		$strReq = 'DELETE FROM '.$this->table.' '.
				"WHERE blog_id = '".$this->con->escape($this->blog->id)."' ".
				'AND link_id = '.$id.' ';
		
		$this->con->execute($strReq);
		$this->blog->triggerBlog();
	}
	
	public function updateOrder($id,$position)
	{
		$cur = $this->con->openCursor($this->table);
		$cur->link_position = (integer) $position;
		
		$cur->update('WHERE link_id = '.(integer) $id.
			" AND blog_id = '".$this->con->escape($this->blog->id)."'");
		$this->blog->triggerBlog();
	}
	
	public function updateLevel($id,$level)
	{
		$cur = $this->con->openCursor($this->table);
		$cur->link_level = (integer) $level;
		
		$cur->update('WHERE link_id = '.(integer) $id.
			" AND blog_id = '".$this->con->escape($this->blog->id)."'");
		$this->blog->triggerBlog();
	}
}