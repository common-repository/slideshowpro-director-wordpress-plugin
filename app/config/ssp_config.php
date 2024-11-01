<?php  

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['folder_name']= plugin_basename(dirname(FCPATH . SELF));
$config['path']       = trailingslashit(dirname(FCPATH . SELF));
$config['url']        = trailingslashit( get_option( 'siteurl' ) . '/wp-content/plugins/'.$config['folder_name']);
$config['upload_dir'] = wp_upload_dir();
$config['version']    = '1.0.1';
$config['options']    = array (
							'ssp_version'                => $config['version'],
							'ssp_director_apikey'		 => '',
							'ssp_director_apipath'		 => '',
							'ssp_director_tscale'		 => 1,
							'ssp_director_vid_controls'  => 1,
							'ssp_director_vid_auto'		 => 0,
							'ssp_img_links'	 			 => 1,
															
							);
							
$config['short_codes'] = array(
							'url' => NULL,
							'width' => NULL,
							'height' => NULL,
							'title' => NULL,
							'type' => NULL,
							'autostart' => NULL,
							'controls' => NULL
							);

//End of file ssp_config.php
//Location /slidshowpro/config/ssp_config.php