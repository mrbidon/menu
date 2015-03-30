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

# Menu template functions
require_once dirname(__FILE__).'/class.dc.blogmenu.php';
require dirname(__FILE__).'/_widgets.php';

$core->tpl->addValue('Menu',array('tplMenu','menu'));
$core->tpl->addValue('MenuFreshy',array('tplMenu','menuFreshy'));

class tplMenu
{
	# Get current theme configuration for menu
	public static function loadStyle($load_style_widget=false)
	{
		global $core;
		
		$current_theme = tplMenu::getTheme();
		$config = path::fullFromRoot($core->blog->settings->system->themes_path.'/'.$current_theme,DC_ROOT).'/menu.'.$current_theme.'.php';
		
		if (!file_exists($config)) {
			$config = dirname(__FILE__).'/themes-config/menu.'.$current_theme.'.php';		
		}

		if (file_exists($config)) {
			require $config;
				
			if ($load_style_widget == false && isset($template_theme_style)) {
				foreach ($template_theme_style as $k => $v) {
						define ($k, $v);
				}
			} elseif (isset($widget_theme_style)) {
				foreach ($widget_theme_style as $k => $v) {
						define ($k, $v);
				}
			}
		}
		return;
	}

	public static function DefineTemplateStyle($attr)
	{
		if (!defined("THEME_STYLE")){

			# Template  style
			$load_style_widget = false;
			self::loadStyle($load_style_widget);

			#Default
			if(!defined("LI_ACTIVE"))
			{
				define ('LI_ACTIVE', 'class="active"');
			}
			
			define ('THEME_STYLE', 'true');
		}
	}

	public static function DefineWidgetStyle()
	{
		if (!defined("W_THEME_STYLE")){

			# Widget style
			$load_style_widget = true;
			self::loadStyle($load_style_widget);

			#Default
			if(!defined("W_LI_ACTIVE"))
			{
				define ('W_LI_ACTIVE', 'class="w-active"');
			}
			
			define ('W_THEME_STYLE', 'true');
		}
	}

	public static function getTheme()
	{
		global $core;
		$theme = $core->blog->settings->system->theme;

		return $theme;
	}

	#  Use {{tpl:MenuFreshy}}
	public static function menuFreshy($attr)
	{
		return
		self::menu($attr);
	}

	#  Use : {{tpl:Menu}}
	# Attribute : level = (0|1|2|.....|n) 
	# Exemples : 
	# {{tpl:Menu}}  or {{tpl:Menu level="1"}} give menu whith only levels 1 (defaut),
	# {{tpl:Menu level="0"}} give menu whith all levels > 0,
	# {{tpl:Menu level="2"}} give menu whith  levels 1 and 2, 
	# etc.
	public static function menu($attr)
	{
		$block = '<ul>%s</ul>';
		$item = '<li>%s</li>';
		$level = 1;

		if (isset($attr['level'])) {
			$level =  abs((integer) $attr['level'])+0;
		}

		//if (isset($attr['block'])) {
		//	$block = addslashes($attr['block']);
		//}
			
		//if (isset($attr['item'])) {
		//	$item = addslashes($attr['item']);
		//}
		
		tplMenu::DefineTemplateStyle($attr);
		$a = '';
		$style_widget = false;
		$style_theme = self::Style($style_widget);
		
		$a = "\$style_theme = array( ";
		foreach ($style_theme as $k => $v) {
			$a .= "'".$k."' => '".addslashes($v)."',";
		}
		$a .= " ); ";

		$res = '<?php ';
		$res .= $a;
		$res .= "echo tplMenu::getList('".$block."','".$item."','".$level."',\$style_theme); ";
		$res .= '?>'."\n";
		
		return $res;
	}

	public static function getList($block='<ul>%s</ul>',$item='<li>%s</li>',$level=1,$style_theme=array())
	{
		$params['level'] = $level;
		$params['desc'] = true;

		$menu = new dcBlogMenu($GLOBALS['core']->blog);
		try {
			$links = $menu->getLinks($params);
		} catch (Exception $e) {
			return false;
		}

		return self::getLinksList($links,$block,$item,$style_theme);
	}

	public static function firstLevelCount()
	{
		$params = array();
		$params['link_level'] = 1;
		
		$menu = new dcBlogMenu($GLOBALS['core']->blog);
		try {
			$linkslevels = $menu->getLinksLevels($params);
		} catch (Exception $e) {
			return false;
		}

		return $linkslevels->count();
	}

	private static function getLinksList($links,$block='<ul>%s</ul>',$item='<li>%s</li>',$style_theme=array())
	{
		# Current relative URL
		$url = $_SERVER['REQUEST_URI'];
		$abs_url = http::getHost().$url;
		
		# First level Count
		$first_level_count = self::firstLevelCount();

		$level = $first_level_number = 0;
		$old_level = $child_level = 1;
		$style = $list = '';

		# For detect if home
		$home_url = html::stripHostURL($GLOBALS['core']->blog->url);
		$home_directory = dirname($home_url);
		if ( $home_directory != '/') { $home_directory = $home_directory.'/'; }

		foreach ($links->rows() as $k => $v)
		{
			$_parent = $style_actv_sm = $close = '';
			
			$id =  $v['link_id'];
			$title = $v['link_title'];
			$href  = $v['link_href'];
			$level  = $v['link_level'];
			$desc = $v['link_desc'];
			$lang  = $v['link_lang'];
			$class = $v['link_class'];
			
			$href = html::escapeHTML($href);
			
			# Detect if active item ( or if homepage on home item  :  because should have two url )  
			if($url == $href || $abs_url == $href || ($_SERVER['URL_REQUEST_PART'] == '' && ($href == $home_url || $href == $home_directory)))
			{
				$style = '_actv';
				
				# Parent when active link?
				if ($style_theme['li_actv_sm'] != '') {
					if ($level > 1) {
						$style_actv_sm = '_sm';
						$goto_parent = 1;
						$child_level = $level;
					} elseif (!empty($goto_parent)) {
						$_parent = '_prnt';
						$goto_parent = 0;
						$child_level = $level;
					}
				}
			} else {
				$style = '';
				
				if ($style_theme['li_actv_sm'] != '') {				
					# Parent when not active link?
					if (!empty($goto_parent)) {
						if ($level < $child_level){
							$_parent = (($level == 1) ? '_prnt' : '_smprnt');
							if ($level == 1) {
								$goto_parent = 0;
							}
							$child_level = $level;
						}
					} else {
						$child_level = 1;
					}
				}
			}

			# Position
			if($level == 1) {
				$first_level_number++;
				if ($first_level_number == $first_level_count) {
					$style .= '_frst';
				} elseif ($first_level_number == 1) {
					$style .= '_lst';
				}
			}			
			
			# Link generator
			$link =
			'<a href="'.html::escapeHTML($href).'"'.stripslashes($style_theme['li_a'.$style]).
			((!$lang) ? '' : ' hreflang="'.html::escapeHTML($lang).'"').
			((!$desc) ? '' : ' title="'.html::escapeHTML($desc).'"').
			'>'.sprintf(stripslashes($style_theme['a_span']),html::escapeHTML($title)).'</a>';

			# add class to each link :
			if ($class == '') { $class = 'menu-item-'.$id; }
			
			# Level's menu generator
			$item = '<li'.sprintf(stripslashes($style_theme['li'.$style.$_parent.$style_actv_sm]),$class).'>%s';

			if($level == $old_level) {
				$close = '</li>';

			} elseif ($level < $old_level) {
				$close = '<ul'.stripslashes($style_theme['ul_sm']).'>';

			} else {
				$diff_level = $level - $old_level;

				while ($diff_level != 0) {
				$close .= '</li></ul>';
				$diff_level--;
				}
				$close .= '</li>';
			}

			$list = sprintf($item.$close,$link).$list;
		
			$old_level = $level;
		}
		return sprintf(stripslashes($style_theme['block']),$list)."\n";
	}
	
	public static function Style($style_widget=false)
	{
		$style_theme = array();
		
		if($style_widget == true) {
			$style_theme['li'] = (defined("W_LI") ? ' '.W_LI : '');
			$style_theme['li_actv'] = (defined("W_LI_ACTIVE") ? ' '.W_LI_ACTIVE : $style_theme['li']);
			$style_theme['li_frst'] = (defined("W_LI_FIRST") ? ' '.W_LI_FIRST : $style_theme['li']);
			$style_theme['li_actv_frst'] = (defined("W_LI_ACTIVE_FIRST") ? ' '.W_LI_ACTIVE_FIRST : $style_theme['li_actv']);
			$style_theme['li_lst'] = (defined("W_LI_LAST") ? ' '.W_LI_LAST : $style_theme['li']);
			$style_theme['li_actv_lst'] = (defined("W_LI_ACTIVE_LAST") ? ' '.W_LI_ACTIVE_LAST : $style_theme['li_actv']);

			$style_theme['li_actv_sm'] = (defined("W_LI_ACTIVE_SM") ? ' '.W_LI_ACTIVE_SM : '');
			
			//if (defined("W_LI_ACTIVE_SM")) {
			
			$style_theme['li_prnt'] = (defined("W_LI_PARENT") ? ' '.W_LI_PARENT : '');
			$style_theme['li_actv_prnt'] = (defined("W_LI_ACTIVE_PARENT") ? ' '.W_LI_ACTIVE_PARENT : $style_theme['li_prnt']);
			$style_theme['li_frst_prnt'] = (defined("W_LI_FIRST_PARENT") ? ' '.W_LI_FIRST_PARENT : $style_theme['li_prnt']);
			$style_theme['li_actv_frst_prnt'] = (defined("W_LI_ACTIVE_FIRST_PARENT") ? ' '.W_LI_ACTIVE_FIRST_PARENT : $style_theme['li_actv_prnt']);
			$style_theme['li_lst_prnt'] = (defined("W_LI_LAST_PARENT") ? ' '.W_LI_LAST_PARENT : $style_theme['li_prnt']);
			$style_theme['li_actv_lst_prnt'] = (defined("W_LI_ACTIVE_LAST_PARENT") ? ' '.W_LI_ACTIVE_LAST_PARENT : $style_theme['li_actv_prnt']);
			
			$style_theme['li_smprnt'] = (defined("W_LI_SMPARENT") ? ' '.W_LI_SMPARENT : '');
			
			//}

			# To add a specified class to each link :
			foreach ($style_theme as $k => $v) {
				if ($v != '') {
					//$patern = '#class="(.+?)(")#';
					$patern = '#class="#';
					if (preg_match($patern,$v,$m)) {
						$style_theme[$k] = str_replace('class="','class="%s ',$v);						
					} else {
						$style_theme[$k] = $v.' class="%s"';
					}
				} else {
					$style_theme[$k] = ' class="%s"';
				}
			}
			
			$style_theme['ul'] = (defined("W_UL") ? ' '.W_UL : '');
			
			$style_theme['li_a'] = (defined("W_LI_A") ? ' '.W_LI_A : '');
			$style_theme['li_a_actv'] = (defined("W_LI_A_ACTIVE") ? ' '.W_LI_A_ACTIVE : $style_theme['li_a']);
			$style_theme['li_a_frst'] = (defined("W_LI_A_FIRST") ? ' '.W_LI_A_FIRST : $style_theme['li_a']);
			$style_theme['li_a_actv_frst'] = (defined("W_LI_A_ACTIVE_FIRST") ? ' '.W_LI_A_ACTIVE_FIRST : $style_theme['li_a_actv']);
			$style_theme['li_a_lst'] = (defined("W_LI_A_LAST") ? ' '.W_LI_A_LAST : $style_theme['li_a']);
			$style_theme['li_a_actv_lst'] = (defined("W_LI_A_ACTIVE_LAST") ? ' '.W_LI_A_ACTIVE_LAST : $style_theme['li_a_actv']);
			
			$style_theme['ul_sm'] = (defined("W_UL_SM") ? ' '.W_UL_SM : '');

			$style_theme['block'] = '<ul'.$style_theme['ul'].'>%s</ul>';
			# for widget link  menu like : <a href="link-url"><span>Title</span></a>
			$style_theme['a_span'] = (defined("W_A_SPAN") ? '<span>%s</span>' : '%s');
			
			$style_theme['a_span'] = (defined("W_A_CUSTOMIZED") ? W_A_CUSTOMIZED : $style_theme['a_span']);
		//$style_widget == false;
		} else {
			$style_theme['li'] = (defined("LI") ? ' '.LI : '');
			$style_theme['li_actv'] = (defined("LI_ACTIVE") ? ' '.LI_ACTIVE : $style_theme['li']);
			$style_theme['li_frst'] = (defined("LI_FIRST") ? ' '.LI_FIRST : $style_theme['li']);
			$style_theme['li_actv_frst'] = (defined("LI_ACTIVE_FIRST") ? ' '.LI_ACTIVE_FIRST : $style_theme['li_actv']);
			$style_theme['li_lst'] = (defined("LI_LAST") ? ' '.LI_LAST : $style_theme['li']);
			$style_theme['li_actv_lst'] = (defined("LI_ACTIVE_LAST") ? ' '.LI_ACTIVE_LAST : $style_theme['li_actv']);

			$style_theme['li_actv_sm'] = (defined("LI_ACTIVE_SM") ? ' '.LI_ACTIVE_SM : '');
			
			//if (defined("LI_ACTIVE_SM") != '') {
			
			$style_theme['li_prnt'] = (defined("LI_PARENT") ? ' '.LI_PARENT : '');
			$style_theme['li_actv_prnt'] = (defined("LI_ACTIVE_PARENT") ? ' '.LI_ACTIVE_PARENT : $style_theme['li_prnt']);
			$style_theme['li_frst_prnt'] = (defined("LI_FIRST_PARENT") ? ' '.LI_FIRST_PARENT : $style_theme['li_prnt']);
			$style_theme['li_actv_frst_prnt'] = (defined("LI_ACTIVE_FIRST_PARENT") ? ' '.LI_ACTIVE_FIRST_PARENT : $style_theme['li_actv_prnt']);
			$style_theme['li_lst_prnt'] = (defined("LI_LAST_PARENT") ? ' '.LI_LAST_PARENT : $style_theme['li_prnt']);
			$style_theme['li_actv_lst_prnt'] = (defined("LI_ACTIVE_LAST_PARENT") ? ' '.LI_ACTIVE_LAST_PARENT : $style_theme['li_actv_prnt']);
			
			$style_theme['li_smprnt'] = (defined("LI_SMPARENT") ? ' '.LI_SMPARENT : '');
			
			//}
			
			# To add a specified class to each link :
			foreach ($style_theme as $k => $v) {
				if ($v != '') {
					//$patern = '#class="(.+?)(")#';
					$patern = '#class="#';
					if (preg_match($patern,$v,$m)) {
						$style_theme[$k] = str_replace('class="','class="%s ',$v);						
					} else {
						$style_theme[$k] = $v.' class="%s"';
					}
				} else {
					$style_theme[$k] = ' class="%s"';
				}
			}

			$style_theme['ul'] = (defined("UL") ? ' '.UL : '');
			
			$style_theme['li_a'] = (defined("LI_A") ? ' '.LI_A : '');
			$style_theme['li_a_actv'] = (defined("LI_A_ACTIVE") ? ' '.LI_A_ACTIVE : $style_theme['li_a']);
			$style_theme['li_a_frst'] = (defined("LI_A_FIRST") ? ' '.LI_A_FIRST : $style_theme['li_a']);
			$style_theme['li_a_actv_frst'] = (defined("LI_A_ACTIVE_FIRST") ? ' '.LI_A_ACTIVE_FIRST : $style_theme['li_a_actv']);
			$style_theme['li_a_lst'] = (defined("LI_A_LAST") ? ' '.LI_A_LAST : $style_theme['li_a']);
			$style_theme['li_a_actv_lst'] = (defined("LI_A_ACTIVE_LAST") ? ' '.LI_A_ACTIVE_LAST : $style_theme['li_a_actv']);
			
			$style_theme['ul_sm'] = (defined("UL_SM") ? ' '.UL_SM : '');
			
			$style_theme['block'] = '<ul'.$style_theme['ul'].'>%s</ul>';
			# for widget link  menu like : <a href="link-url"><span>Title</span></a>
			$style_theme['a_span'] = (defined("A_SPAN") ? '<span>%s</span>' : '%s');
			
			$style_theme['a_span'] = (defined("A_CUSTOMIZED") ? A_CUSTOMIZED : $style_theme['a_span']);
		}
		return $style_theme;
	}
	
	# Widget function
	public static function menuWidget($w)
	{
		global $core;

		if ($w->offline)
			return;

		if (($w->homeonly == 1 && $core->url->type != 'default') ||
			($w->homeonly == 2 && $core->url->type == 'default'))
			return;
		
		$style_widget = true;
		tplMenu::DefineWidgetStyle();
		$style_theme = self::Style($style_widget);
		
		$level = 0;
		return
		
		$res =
    ($w->content_only ? '' : '<div class="widget w-block-menu'.($w->class ? ' '.html::escapeHTML($w->class) : '').'">').
		($w->title ? $w->renderTitle(html::escapeHTML($w->title)) : '').
		self::getList('<ul>%s</ul>','<li>%s</li>',0,$style_theme).
		($w->content_only ? '' : '</div>');
	}
	
	public static function getLevels($params)
	{
		$menu = new dcBlogMenu($GLOBALS['core']->blog);

		try {
			$linkslevels = $menu->getLinksLevels($params);
		} catch (Exception $e) {
			return false;
		}	
		return $linkslevels;
	}
	
	public static function endKey($array) {
		end($array);
		
		return key($array);
	}
}