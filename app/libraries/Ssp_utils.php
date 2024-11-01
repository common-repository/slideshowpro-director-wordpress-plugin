<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

 /**
 * Utility class for SlideShowPro plugin
 *
 * @package		SlideShowPro
 * @author		Dominey Design Inc.
 * @copyright	Copyright (c) 2010, Dominey Design Inc.
 * @link		http://slideshowpro.net
 * @version 	1.0
 * @lastedit	05.28.2010
 */

class Ssp_utils {

	/**
	* Class Constructor 
	*
	* Sets the SP super variable
	* and assigns the db variable
	*
	* @param string - slidepress database table
	* @access public
	* @return string - slidepress version
	*/

    function Ssp_utils()
    {
		$this->SP =& get_instance(); 
		$this->db  =& $this->SP->wpdb; 
		log_message( 'debug', 'Loading Utils Class' );
    }

	/**
	* Gets Options 
	*
	* Compares slidepress default options
	* with those in database, adds mission
	* options and sets null values to default
	*
	* @param array  - options from config file
	* @param array  - options from wp database
	* @access public
	* @return array - updated options
	*/
	
	function get_options($config_options = array(), $db_options = array() ) 
	{
		$opts = array();		
		foreach( $config_options as $option => $value )
		{
		if( empty( $db_options[$option] ) || $db_options[$option] == '' || $db_options[$option] == NULL )
			{
				$opts[$option] = $value;
			} else {
				$opts[$option] = $db_options[$option];
			}
						
			log_message('debug', $option . ' = ' . $opts[$option] );
		}
		
		return $opts;
	}
	
	/**
	* Convert Galleries Array to Json
	*
	* @param array  - galleries array
	* @access public
	* @return string - json representation of galleries
	*/
	
	function galleries_to_json( $galleries ) {
		$results = array();
		foreach ( $galleries as $gallery ) 
		{
			$properties = array();
			foreach ( $gallery as $property => $value ) 
			{
				$properties[] = "{$property} : '{$value}'";
			}	
			$results[] = '{' . implode( ',', $properties ) . '}';
		}		
		return '[' . implode( ',', $results ) . ']';
	}
	
	
	/**
	* Get Domain Name
	*
	* parses a url for the domain
	* name and strips the www
	*
	* @param string - url to parse
	* @access public
	* @return string
	*/
	
	function get_this_domain( $url = '' )
	{
		$domain = ereg_replace('www\.','',$url);
		$domain = parse_url($domain);
		if( !empty($domain["host"] ) )
		{
			 return $domain["host"];
	    } else {
			 return $domain["path"];
		}
	}
	  
	  
	 /**
	 * Creates an object from an array
	 *
	 * @param array new params
	 * @param array default params
	 * @access public
	 * @return string
	 */	
	 
	 function params_to_object( $p, $d ) {
		$params =  $this->SP->
		$exclude = array(
						'sspName',
						'sspWidth',
						'sspHeight',
						'xmlFileType',
						'createThumbnails'
						);
		
		$param_names = array_keys($ssp_params);
		$param_names = array_diff($param_names, $exclude);
		$properties = array();
		foreach ( $param_names as $param_name ) 
		{
			$v = $p[$param_name];
			if( empty( $v ) ) continue;
		
			$properties[] = $param_name.':"'.$v.'"';
		}
		
		$results = '{' . implode( ',', $properties ) . '}';
		return $results;
	}
	
	 /**
	 * Verify good api key + path
	 *
	 * @param string apikey
	 * @param string path
	 * @access public
	 * @return boolean
	 */	
	
	function verify_director($api_key, $path)
	{
		if (empty($api_key) || empty($path)) {
			return FALSE;
		}
		
		if (strpos( $api_key, '-') !== FALSE ) {
			preg_match('/^(local|hosted)\-(.*)/', $api_key, $matches);
			if ( !empty($matches[1]) ) {
				return $matches[1];
			}
		
		} else {
			return FALSE;
		}	
	}
	
	/**
	 * Download and Save Image
	 * from SlideShowPro Director
	 *
	 * @param string 
	 * @access public
	 * @return path
	 */	
	 
	 function download_and_save_remote_image( $url, $id, $filename )
	 {		
	 	global $wpdb;
	 	
	 	$parsed_url = @parse_url( $url );
	 	if ( !$parsed_url || !is_array( $parsed_url ) ) return false;

	 		$dir = wp_upload_dir();
	 		$path = $dir['path'];
	 		$new_url = $dir['url'];
	 		
			$filename = wp_unique_filename( $path, $filename, $unique_filename_callback = null );
			$wp_filetype = wp_check_filetype($filename, null );
			$fullpathfilename = $path . "/" . $filename;
			if ( !substr_count($wp_filetype['type'], "image") ) {
				log_message("error", basename($url) . ' is not a valid image. ' . $wp_filetype['type']  . '' );
				return basename($url) . ' is not a valid image. ' . $wp_filetype['type']  . '' ;
			}
			
			$opts = array();
			$opts['timeout'] = 30;
			
			if( !$response = wp_remote_get( $url, $opts ) ) return false;

			$image_string = $response['body'];
			$fileSaved = file_put_contents( $path . "/" . $filename, $image_string);
			if ( !$fileSaved ) {
				log_message("error", "The file cannot be saved.");
				return "The file cannot be saved at " . $path . "/" . $filename;
			}
			
			$attachment = array(
				 'post_mime_type' => $wp_filetype['type'],
				 'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
				 'post_content' => '',
				 'post_status' => 'inherit',
				 'guid' => $new_url . "/" . $filename
			);
			
			if ( !($attach_id = wp_insert_attachment( $attachment, $fullpathfilename, $id ) ) ) {
				log_message("error", "Failed to save record into database.");
				return "Failed to save record into database.";
			}
			require_once(ABSPATH . "wp-admin" . '/includes/image.php');
			$attach_data = wp_generate_attachment_metadata( $attach_id, $fullpathfilename );
			wp_update_attachment_metadata( $attach_id,  $attach_data );
			
			return $attach_id;
	 }
	 
	 function deregister_scripts_styles( $handles = array() ) 
	 {   
		(array)$handles;
		    
		 foreach($handles as $handle){
		 	wp_deregister_style( $handle);     
		 	wp_deregister_script( $handle);   
		 }  
	 }
}

/* End Utils Class */
/* Location slideshowpro/ctrl/app/libraries/utils.php */