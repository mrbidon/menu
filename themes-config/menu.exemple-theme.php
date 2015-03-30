<?php
# ******* Template style menu *******
$template_theme_style = array(
	// *** All possibles templates's menu styles *** :
	//Following "Class" and "id" are only for exemples but can be your ones.
	
	//External ul
	'UL'					=> 'id="menu"',
	
	// Top level's  menu :
	
	# Top level's menu item
	'LI'					=> '',
	'LI_ACTIVE'				=> 'class="active"',
	'LI_FIRST'				=> 'class="first"',
	'LI_ACTIVE_FIRST'		=> 'class="active first"',
	'LI_LAST'				=> 'class="last"',
	'LI_ACTIVE_LAST'		=> 'class="active last"',
	# Link of Top level's menu item
	'LI_A'					=> '',
	'LI_A_ACTIVE'			=> '',
	'LI_A_FIRST'			=> '',
	'LI_A_ACTIVE_FIRST'		=> '',
	'LI_A_LAST'				=> '',
	'LI_A_ACTIVE_LAST'		=> '',
	# Top level item, parent item of sub current, or sub sub current item
	'LI_PARENT'				=> 'class="parent"',
	'LI_ACTIVE_PARENT'		=> 'class="active parent"',
	'LI_FIRST_PARENT'		=> 'class="first parent"',
	'LI_ACTIVE_FIRST_PARENT'=> 'class="active first parent"',
	'LI_LAST_PARENT'		=> 'class="last parent"',
	'LI_ACTIVE_LAST_PARENT'	=> 'class="active last parent"',

	// Sub  level's menu :
	
	# Sub ul
	'UL_SM'					=> '',
	# Sub or sub sub current item
	'LI_ACTIVE_SM'			=> 'class="active"',
	#  Sub item, parent of sub sub current item
	'LI_SMPARENT'			=> 'class="sm-parent"',
	
	// This Formating all links like : <a href="link-url"><span>Title</span></a>
	'A_SPAN'				=> 'true',
	// This Formating all links like : <a href="link-url"><span class="title">Title</span><span class="pointer"></span></a>
	'A_CUSTOMIZED'			=> '<span class="title">%s</span><span class="pointer"></span>',
);

# ******* Widget style menu *******
$widget_theme_style = array(
	// *** All possibles templates's menu styles *** :
	// Following "Class" and "id" are only for exemples but can be your ones.
	
	//External ul
	'W_UL'						=> 'id="widget-menu"',
	
	// Top level's  menu :
	
	# Top level's menu item
	'W_LI'						=> '',
	'W_LI_ACTIVE'				=> 'class="active"',
	'W_LI_FIRST'				=> 'class="first"',
	'W_LI_ACTIVE_FIRST'			=> 'class="active first"',
	'W_LI_LAST'					=> 'class="last"',
	'W_LI_ACTIVE_LAST'			=> 'class="active last"',
	# Link of Top level's menu item
	'W_LI_A'					=> '',
	'W_LI_A_ACTIVE'				=> '',
	'W_LI_A_FIRST'				=> '',
	'W_LI_A_ACTIVE_FIRST'		=> '',
	'W_LI_A_LAST'				=> '',
	'W_LI_A_ACTIVE_LAST'		=> '',
	# Top level item, parent item of sub current, or sub sub current item
	'W_LI_PARENT'				=> 'class="parent"',
	'W_LI_ACTIVE_PARENT'		=> 'class="active parent"',
	'W_LI_FIRST_PARENT'			=> 'class="first parent"',
	'W_LI_ACTIVE_FIRST_PARENT'	=> 'class="active first parent"',
	'W_LI_LAST_PARENT'			=> 'class="last parent"',
	'W_LI_ACTIVE_LAST_PARENT'	=> 'class="active last parent"',

	// Sub  level's menu :
	
	# Sub ul
	'W_UL_SM'					=> '',
	# Sub or sub sub current item
	'W_LI_ACTIVE_SM'			=> 'class="active"',
	#  Sub item, parent of sub sub current item
	'W_LI_SMPARENT'				=> 'class="sm-parent"',
	
	// This Formating all links like : <a href="link-url"><span>Title</span></a>
	'W_A_SPAN'					=> 'true',
	// This Formating all links like : <a href="link-url"><span class="title">Title</span><span class="pointer"></span></a>
	'W_A_CUSTOMIZED'			=> '<span class="title">%s</span><span class="pointer"></span>',
);