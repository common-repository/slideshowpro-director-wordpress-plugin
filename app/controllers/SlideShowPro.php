<?php
# cannot call this file directly
if ( strpos( basename( $_SERVER['PHP_SELF']) , __FILE__ ) !== false ) exit;

class slideshowpro extends Controller
{	
	 /*
	 * slideshowpro constructor
	 */
	 
	function SlideShowPro()
	{
		$this->__construct();
	}
	
	function __construct()
	{
		parent::Controller();
		
		global $post;
		
		#Load config file
		$this->config->load('ssp_config', TRUE);
		
		#set dbase var
		$this->db =& $wpdb;
		
		#Assing config array
		$this->ssp_config = $this->config->item('ssp_config');
		
		#Load Javascript Class
		$this->load->library( 'ssp_javascript', array(), 'javascript' );
		
		#Load Utilities Class
		$this->load->library('ssp_utils', $this->ssp_config, 'utils' );
		
		#Load Model
		$this->load->model('slideshowpro_settings_model');
		
		#Set var to store slideshowpro options
		$this->options = array();
		
		#site url
		$this->site_url = get_bloginfo( 'wpurl' );
		
		#set slideshowpro url from config
		$this->url = $this->ssp_config['url'];
		
		#js path
		$this->js_url = $this->url . 'app/javascript/';
		
		$this->script_foot = '';
		
		#post types
		$this->post_types = array('slideshowpro', 'slideshowpro_styles' );
		
		#set post_type
		$this->post_type =& get_post_type( $post );
		
		#director embed
		$this->director_embed = FALSE;
				
	 /**
	 * Register Wp actions
	 *
	 * Provides wordpress with a
	 * blueprint to orchesrate how
	 * and when to run slideshowpro
	 * functions
	 *
	 */	
	 
	 	#get saved options
		add_action( 'admin_init', array( &$this, 'action_init_options' ), 20 );
		    
		#initializes slideshowpro setup options
		add_action( 'admin_menu', array( &$this, 'action_init_menu' ) );
				
		#loads styles for front-end pages                 
		add_action( 'wp_print_styles' , array(&$this, 'frontend_styles' ) );
		
		#loads scripts for front-end pages				 
		add_action( 'wp_print_scripts' , array(&$this, 'frontend_scripts' ) );
		
		#registers parametized action hooks
		add_action( 'after_plugin_row_slideshowpro/slideshowpro.php', array(&$this, 'action_notification' ) );
		
		add_action('admin_notices_slideshowpro-settings', array(&$this, 'ssp_warning') );

		#media tabs
		add_action('media_buttons', array(&$this, 'addMediaButton'), 20);
		add_action('media_upload_slideshowpro', array(&$this, 'slideshowpro_create_iframe'), 100);
		add_action('admin_print_styles-slideshowpro-media-popup', array(&$this, 'admin_slideshowpro_media_styles' ), 100);
		add_action('admin_print_scripts-slideshowpro-media-popup', array(&$this, 'admin_slideshowpro_media_scripts'), 100);
		
		#ajax
		add_action('wp_ajax_download_and_save_remote_image', array( &$this, 'action_download_and_save_remote_image' ) );

		#registers slideshowpro shortcode
		add_shortcode('slideshowpro', array(&$this, 'shortcode' ) );
		
		#activation + deactivation hooks
		
		$file = SSP_BASENAME;
		log_message('debug', $file );
		
		register_activation_hook($file, array(&$this, 'action_activate' ) );
		register_deactivation_hook($file, array(&$this, 'action_deactivate' ) );
		
	}
	
	
	 /**
	 * Activate
	 *
	 * Activates plugin creates dependancies runs updates
	 *
	 * @access	public
	 * @return	void
	 */	
	 
	function action_activate()
	{	
		ob_start();	
		register_setting( 'slideshowpro', 'slideshowpro_settings' );
		#place options in array
		$options = get_option( 'slideshowpro_settings' );
		
		$slideshowpro_settings = array();
		
		#compare with config options
		$slideshowpro_settings = $this->utils->get_options( $this->ssp_config['options'], $options );
		update_option( 'slideshowpro_settings', $slideshowpro_settings );
		ob_end_clean();
	}
	
	 /**
	 * Deactivate
	 *
	 * Deactivates plugin
	 *
	 * @access	public
	 * @return	void
	 */	
	 
	function action_deactivate()
	{
		//delete_option( 'slideshowpro_settings' );
	}
	
		
	 /**
	 * Admin Options
	 *
	 * Gets Options and registers settings
	 * checks options saved in wp aginst
	 * config array to determine if
	 * options need to be added or set
	 *
	 * registers stylesheets with wp
	 *
	 * @access	public
	 * @return	void
	 */	
	
	function action_init_options() 
	{
		log_message( 'debug', 'Initializing options' );
		
		register_setting( 'slideshowpro', 'slideshowpro_settings' );
		
		#place options in array
		$this->options = get_option( 'slideshowpro_settings' );
		
		$slideshowpro_settings = array();
		if( ! is_array( $this->options) )
		{
			#compare with config options
			$slideshowpro_settings = $this->utils->get_options( $this->ssp_config['options'], $this->options );
			update_option( 'slideshowpro_settings', $slideshowpro_settings );
			
			$this->options = $slideshowpro_settings;
		}
		#set slideshowpro url from config
		$this->url = $this->ssp_config['url'];
		log_message('debug', 'SP URL: '.$this->url );
		
		#site url
		$this->site_url = get_bloginfo( 'wpurl' );
		log_message('debug', 'WP URL: '.$this->site_url );
		
		#js path
		$this->js_url = $this->url . 'app/javascript/';
		log_message('debug', 'JS URL: '.$this->js_url );
		
		#register styles unique to slideshowpro with wp
		wp_register_style( 'slideshowpro_styles', $this->url . 'css/slideshowpro.css' );
			
		#register scripts
		wp_register_script( 'slideshowpro',$this->js_url .'slideshowpro.js', array( 'jquery' ) );
		wp_register_script( 'modernizr', $this->js_url .'modernizr-1.5.min.js', array( 'jquery' ) );
		
		#Check Director 
		$this->director_key =  $this->options['ssp_director_apikey'];
		$this->director_path = str_replace('http://', '', rtrim($this->options['ssp_director_apipath'], '/'));
		
		$dir = $this->utils->verify_director( $this->director_key, $this->director_path );
		
		#is this hosted or local
		$this->dir_hosted = ( 'hosted' == $dir ) ? 'true' : 'false';
		
		$this->director_embed = $dir;			
		
		if( $this->director_embed )
		{
			$this->path_to_director_js = "http://" . $path . "/m/embed.js";
			
			#register DirectorJS
			wp_register_script( 'DirectorEmbedJS', $this->path_to_director_js );
		}
	}
	
	
     /**
	 * Init Admin Menu
	 *
	 * Builds admin menu and assigns
	 * scripts/styles to the pages 
	 * based on functionality required
	 *
	 * @access	public
	 * @return	menu
	 */	
	
	function action_init_menu()
	{

		log_message( 'debug', 'Initializing menu' );
		$menu = $this->ssp_config['menu_id'];
		
		#build menu and assign handles 
		$home_page = add_options_page( 'SlideShowPro Admin Settings', 'SlideShowPro', 'manage_options', 'slideshowpro-settings' , array( &$this, 'admin_page_setup' ) );
		add_action( "admin_print_scripts-$home_page", array( &$this, 'admin_setup_scripts' ) );
		add_action( "admin_print_styles-$home_page", array( &$this, 'admin_setup_styles' ) );
		
		add_filter('plugin_action_links_' . SSP_BASENAME, array(&$this, 'filter_plugin_actions'), 10, 2 );
		}

		/**
		 * @desc Adds the Settings link to the plugin activate/deactivate page
		 */
	function filter_plugin_actions($links, $file) {
		$settings_link = '<a href="options-general.php?page=slideshowpro-settings">' . __('Settings') . '</a>';
		array_unshift($links, $settings_link); // before other links

		return $links;
	}

		
	 /**
	 * Update Notification  
	 *
	 * Adds update notification when the plugin has an update
	 *
	 * @param string
	 * @access public
	 * @return void
	 */
	 
	function action_notification( $plugin )
	{
		$current = get_option( 'update_plugins' );
		if ( ! empty( $current ) && isset( $current->response[$plugin] ) ):
			$release   = $current->response[$plugin];
			$file_name = "http://slideshowpro.net/releases/{$release->new_version}.txt";
			$response  = wp_remote_get( $file_name );
			if ( ! is_wp_error( $response ) && $response['response']['code'] == 200 ):
				$temp    = explode('|', $response['body']);
				$class   = $temp[0];
				$title   = $temp[1];
				$message = $temp[2];
				echo "<tr class='slideshowpro-update-info'><td class='{$class}' colspan='5'><h6>{$title}</h6><div>{$message}</div></td></tr>";
			endif;
		endif;
	}
	

	
	function ssp_warning() {
		if ( $this->director_embed )  {
			echo '<div class="updated"><p><strong>SlideShowPro Director plugin activated</strong>. Please <a href="options-general.php?page=slideshowpro-settings" title="SlideShowPro Director Settings">enter your Director API Key and Path</a> to start publishing.</p></div>';
			return;
		}
	}
	
	 /**
	 * Download And Save Remote Image  
	 *
	 * @param string url of image
	 *
	 * @access public
	 * @return int attachment id
	 */
		
	function action_download_and_save_remote_image()
	{
		$url = $this->input->post( 'url', TRUE );
		$post_id  = $this->input->post( 'post_id', TRUE );
		$src  = $this->input->post( 'src', TRUE );
		if( $attachment_id = $this->utils->download_and_save_remote_image( $url, $post_id, $src ) )
		{
			echo $attachment_id;
			exit();
		}
		
		echo 0;
		exit();
	}
	
	 /**
	 * Common Admin Scripts
	 *
	 * Loads scripts shared by all admin pages
	 *
	 * @access public
	 * @return void
	 */	
	
	function _common_admin_scripts()
	{	
		log_message( 'debug', 'Loading Common Admin Scripts' );
		wp_enqueue_script( 'jquery' );
		wp_localize_script( 'jquery', 'slideshowpro', array( 'siteurl' => $this->site_url, 'sspurl' => $this->url ) );
		wp_enqueue_script( 'slideshowpro', array('jquery') );
		wp_enqueue_script( 'modernizr' );
	}
	
	 /**
	 * Common Admin Styles
	 *
	 * Loads styles shared by all admin pages
	 *
	 * @access private
	 * @return void
	 */	
	
	function _common_admin_styles()
	{
		log_message( 'debug', 'Loading Common Admin Styles' );
		wp_admin_css( 'dashboard' );
		wp_enqueue_style('slideshowpro_styles');
	}
	
	 /**
	 * Frontend Styles
	 *
	 * adds styles to front end when appropriate
	 *
	 * @access private
	 * @return void
	 */	
	
	function frontend_styles()
	{
		log_message( 'debug', 'Frontend Styles' );
	}
	
	 /**
	 * Frontend Scripts
	 *
	 * adds scripts to front end when appropriate
	 *
	 * @access private
	 * @return void
	 */	
	 
	function frontend_scripts()
	{
		if( !is_admin() )
		{
			log_message( 'debug', 'Loading Frontend Scripts' );
			wp_register_script( 'modernizr', $this->js_url .'modernizr-1.5.min.js', array( 'jquery' ) );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'modernizr' );
			wp_enqueue_script('swfobject');
			
		}			

	}
	
	
	 /**
	 * slideshowpro Settings
	 *
	 * Creates a page with a
	 * form to handle administration
	 * of slideshowpro settings
	 *
	 * @access public
	 * @return void
	 */	
	function admin_page_setup()
	{	
		#get form helper
		$this->load->helper('form');
		
		$data['options'] =  get_option('slideshowpro_settings');
		$data['ssp_config'] = $this->ssp_config;
		$data['roles'] = $this->ssp_config['roles'];
		$data['title'] = "SlideShowPro Director Settings";
		$data['template'] = 'ssp_setup';
		$this->load->view('ssp_template', $data);
	}
	
	function admin_setup_scripts()
	{
		#Add Scripts
		log_message( 'debug', 'Loading Admin Setup Scripts' );
		$this->_common_admin_scripts();
		wp_enqueue_script( 'swfupload-all', array( 'swfobject' ) );		
	}
	
	function admin_setup_styles()
	{
		
		$this->_common_admin_styles();
	}
	
	
	/* Media */

	function admin_slideshowpro_media_styles() 
	{
		global $type, $wp_styles;
		
		$wp_styles->queue = array();
		#register styles unique to slideshowpro with wp
		wp_register_style( 'slideshowpro_styles', $this->url . 'css/slideshowpro.css' );
	
		wp_enqueue_style( 'global' );
		wp_enqueue_style( 'wp-admin' );
		wp_enqueue_style( 'colors' );
		wp_enqueue_style( 'media' );
		wp_enqueue_style( 'slideshowpro_styles' );
	}
	
	function admin_slideshowpro_media_scripts() 
	{
		global $wp_scripts;
		//print_r($wp_scripts);
		
		$wp_scripts->queue = array();
		#register scripts
		
		wp_register_script( 'slideshowpro',$this->js_url .'slideshowpro.js', array( 'jquery' ), 1, 1 );
		
		wp_deregister_script( 'jquery');
		wp_enqueue_script( 'jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js', 0,1,1 );
		wp_enqueue_script('set-post-thumbnail');
		wp_enqueue_script( 'json2', array('jquery') );
		wp_enqueue_script( 'jquery.tmpl', 'http://ajax.microsoft.com/ajax/jquery.templates/beta1/jquery.tmpl.min.js', array('jquery'), 1, 1);
		wp_enqueue_script( 'slideshowpro', array('jquery', 'json2') );
		

	}
	
	function addMediaButton() {
		global $post_ID, $temp_ID;
		
		#If director isn't setup then kill the request.
		 if( FALSE == $this->director_embed  ) return;
		
		$uploading_iframe_ID = (int) (0 == $post_ID ? $temp_ID : $post_ID);
		$media_upload_iframe_src = "media-upload.php?post_id=$uploading_iframe_ID";

		$slideshowpro_upload_iframe_src = apply_filters('media_slideshowpro_iframe_src', "$media_upload_iframe_src&amp;type=slideshowpro");
		
		$slideshowpro_title = 'Add SlideShowPro Director Content';

		$slideshowpro_link_markup = "<a href=\"{$slideshowpro_upload_iframe_src}&amp;tab=slideshowpro&amp;TB_iframe=true&amp;height=500&amp;width=640\" class=\"thickbox\" title=\"$slideshowpro_title\"><img src=\"".$this->ssp_config['url']."/css/img/media-button-ssp.gif\" alt=\"$slideshowpro_title\" /></a>\n";
		
		echo $slideshowpro_link_markup;
        
	}
	
	
	
	function slideshowpro_create_iframe() {
	
		//wp_iframe(array(&$this, 'media_slideshowpro_content'));
		$this->media_slideshowpro_content();
	}
	
	
	
	function modifyMediaTab($tabs) {
	
		return false;
		if( FALSE == $this->director_embed  )
		{
			return array(
        	'slideshowpro_add_slideshow' => 'Slideshow'
        	);
		}
		
        	return array(
            'slideshowpro' =>  __('Image / Video', 'image_video')
        );
        
        log_message('debug', 'Creating Tabs');
    }
    
    
    
	function media_slideshowpro_content() {
		global $tab, $type;
		
	   	if(strpos($_SERVER['REQUEST_URI'], '&slideshowpro_action') > 0)
	   		$_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '&slideshowpro_action'));
		if (strpos($_SERVER['REQUEST_URI'], '&slideshowpro') > 0)
	   		$_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '&slideshowpro'));
		
		add_filter('media_upload_tabs', array(&$this, 'modifyMediaTab'));
		//media_upload_header(); 
			
	    switch ($tab) {
	    	case 'slideshowpro':
	    		$this->slideshowpro_panel();

	    		break;
	   	    	default:
	    		$this->slideshowpro_panel();
	    		break;
	    }
	    
	}
	
	function slideshowpro_panel()
	{
		global $type, $tab, $content_width, $_wp_additional_image_sizes;
		
		
		
		$calling_post_id = 0;
     	if ( $post_id = $this->input->get( 'post_id', TRUE ) ) 
     		$calling_post_id = (int)$post_id;
     	
     	if( function_exists('current_theme_supports' ) ){
	     	if( !current_theme_supports('post-thumbnails') ){
	     		if ( function_exists( 'add_theme_support' ) ) { 
	  				add_theme_support( 'post-thumbnails' ); 
	  				set_post_thumbnail_size( get_option('thumbnail_size_w'), get_option('thumbnail_size_h'), get_option('thumbnail_crop') );
	  				
				}
	     	}
	     }
	     
     	
     	if ( $calling_post_id && current_theme_supports( 'post-thumbnails', get_post_type( $calling_post_id ) ) ){
	        $data['calling_post_id'] = $calling_post_id;
	        }else{
	        $data['calling_post_id'] = 0;
	    }

	    $data['sizes'] = '';
		if ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) ) {
			$sizes = array();
			foreach( $_wp_additional_image_sizes as $size => $value ) {
				//if( $size !== 'post-thumbnail')continue;

				//if( empty( $value['width'] ) ||  empty( $value['height'] ) || empty($size) ) continue;
				
				$ptw = intval( $value['width'] );
				$pth = intval( $value['height'] );
					
				$s = $data['max_thumb_side'] = max($ptw, $pth);
		    	
		   		$sizes[] = "'".str_ireplace('-','_',$size)."'".":{ w:$s, h:$s,c:0,q:85,sh:1,sq:0}";
		   	}
		   	if( count( $sizes ) )
		   		$data['sizes'] = ',' . trim(implode(",", $sizes ), ',');
		}	
		
		
	    $data['no_thumb'] = $this->url . 'css/img/no_thumb.jpg';
	    $data['link_images'] = $this->options['ssp_img_links'] ? 1:0;
		$data['path'] = $this->options['ssp_director_apipath'];
		$data['hosted'] = $this->dir_hosted;
		$data['tscale'] = ( $this->options['ssp_director_tscale'] == 0 ) ? 0 : 1;
		$data['controls'] = ( $this->options['ssp_director_vid_controls'] == 0 ) ? '' : 'checked="checked"';
		$data['auto'] = ( $this->options['ssp_director_vid_auto'] == 0 ) ? '' : 'checked="checked"';
		$data['tw'] = intval(get_option('thumbnail_size_w'));
		$data['th'] = intval(get_option('thumbnail_size_h'));
		$data['mw'] = intval(get_option('medium_size_w'));
		$data['mh'] = intval(get_option('medium_size_h'));
		$data['content_width'] =  isset( $content_width ) ? array($content_width, 'Auto') : array(false, false);
		$data['lh'] = intval(get_option('large_size_h'));
		if( isset( $content_width ) )
		{
			$data['lw'] = intval($content_width);
		} else {
			$data['lw'] = intval(get_option('large_size_w'));
		}

		
		$this->load->view('templates/media/ssp_media_insertjs', $data);
	}
	
	/**** Short Codes ****/
	function shortcode( $atts, $content = NULL ) {
		extract( shortcode_atts( array(
			'url' => NULL,
			'width' => NULL,
			'height' => NULL,
			'title' => NULL,
			'type' => NULL,
			'autostart' => NULL,
			'controls' => NULL,
			'preview' => NULL
			), $atts ) );

		if ( !is_null( $type ) )
		{
			return $this->_display_content( $atts, $content );			
		}

	}
	
	function _display_content( $data, $content )
	{
		if( strpos($data['url'], ".flv" ) || strpos( $data['url'], ".f4v" ) ){
		$data['flv'] = 'true';
			} else {
		$data['flv'] = 'false';
		}
		//$data['autostart'] = $data['autostart'] ? 'true' : 'false';
		//$data['controls'] =  $data['controls'] ? 'true' : 'false';
		$data['vid'] = "video";
		$options = get_option('slideshowpro_settings');
		$path = $options['ssp_director_apipath'];
		$data['path'] = str_replace('http://', '', rtrim($path, '/'));
		$data['embedJS'] = "http://" . $data['path'] . "/m/embed.js";
		$data['directorSWF'] = "http://" . $data['path'] . "/m/slideshowpro.swf";
		$data['unique_id'] = md5( $data['url'] . rand() );
		if( !is_null($data['width']) && is_null($data['height'])  )
		{
			$data['height'] =  ceil(($data['width'] / 3) * 2);
		}
		switch($data['type'])
		{
			case "video":
			return $this->load->view('templates/ssp_video', $data, TRUE );
			break;

			case "slideshow":
			echo wp_remote_fopen($data['url']);
			break;
			
			default:
			echo wp_remote_fopen($data['url']);
		}
	}

}

#End slideshowproCore.php
#Location slideshowpro/ctrl/app/controllers/slideshowproCore.php