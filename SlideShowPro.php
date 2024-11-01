<?php
/*
Plugin Name: SlideShowPro Director Plugin
Plugin URI: http://slideshowpro.net/products/slideshowpro_director/
Description: Post your photos and videos from SlideShowPro Director.
Author: SlideShowPro
Version: 1.0.1
Author URI: http://slideshowpro.net
Copyright 2011 by SlideShowPro
*/

ini_set('display_errors', 0);
error_reporting(0);
//set folder that contains libraries, models, views, etc.
$app_folder = 'app';

//set folder that contains the core
$system_folder = 'core';

//constants used by plugin

//document extension
define( 'EXT', '.php' );

//gets this file name
define( 'SELF', pathinfo(__FILE__, PATHINFO_BASENAME ) );

//gets path to this plugin directory
define( 'FCPATH', str_replace( SELF, '', __FILE__ ) );

//sets path to the app folder (used in classes)
define('BASEPATH', FCPATH . $system_folder.'/');

//sets path to app folder (might be unnecessary
define('APPPATH', FCPATH . $app_folder.'/');

//set the plugin basename
define('SSP_BASENAME', plugin_basename(__FILE__) );

/*
*
* Include the base functions needed to kick 
* things off initialize classes and check php
* enviroment to adjust behavior
*
*/
include_once( BASEPATH . 'includes/base' . EXT );

/* End SlideShowPro.php */
/* Location SlideShowPro.php */