<?php
/**
* Settings model for slidepress plugin
*
* @package		Slidepress
* @author		Dominey Design Inc.
* @copyright	Copyright (c) 2010, Dominey Design Inc.
* @link			http://slideshowpro.net
* @version 		1.0
* @lastedit		05.28.2010
*/

class Slideshowpro_settings_model extends Model {


	function Slideshowpro_settings_model()
	{
		parent::Model();
		global $wpdb;
		$this->db = $wpdb;
	}

	function action_activate() 
	{
		
		
	}
	

	
	
	//deactivate
	function action_deactivate($table_name, $options) {
			}
	
	
	//delete options on deactivation
	function _delete_options($options)
	{
		delete_option( 'ssp_settings' );
	}


	
}
//End  ssp_settings_model