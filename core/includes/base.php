<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//Common functions

function is_php($version = '5.0.0')
{
	static $_is_php;
	$version = (string)$version;
	
	if ( ! isset($_is_php[$version]))
	{
		$_is_php[$version] = (version_compare(PHP_VERSION, $version) < 0) ? FALSE : TRUE;
	}

	return $_is_php[$version];
}

//Tests for file writability

function is_really_writable($file)
{	
	// If we're on a Unix server with safe_mode off we call is_writable
	if (DIRECTORY_SEPARATOR == '/' AND @ini_get("safe_mode") == FALSE)
	{
		return is_writable($file);
	}

	// For windows servers and safe_mode "on" installations we'll actually
	// write a file then read it.  Bah...
	if (is_dir($file))
	{
		$file = rtrim($file, '/').'/'.md5(rand(1,100));

		if (($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE)
		{
			return FALSE;
		}

		fclose($fp);
		@chmod($file, DIR_WRITE_MODE);
		@unlink($file);
		return TRUE;
	}
	elseif (($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE)
	{
		return FALSE;
	}

	fclose($fp);
	return TRUE;
}

//Class registry

function &load_class($class, $instantiate = TRUE)
{
	static $objects = array();

	// Does the class exist?  If so, we're done...
	if (isset($objects[$class]))
	{
		return $objects[$class];
	}

	if (file_exists(BASEPATH.'libraries/'.$class.EXT))
	{
			include_once(BASEPATH.'libraries/'.$class.EXT);
			$is_subclass = FALSE;
	}
	
	
	if ($instantiate == FALSE)
	{
		$objects[$class] = TRUE;
		return $objects[$class];
	}

	if ($is_subclass == TRUE)
	{
		$name = config_item('subclass_prefix').$class;

		$objects[$class] =& instantiate_class(new $name());
		return $objects[$class];
	}

	$name = ($class != 'Controller') ? 'SP_'.$class : $class;

	$objects[$class] =& instantiate_class(new $name());
	return $objects[$class];
}

//Instantiate Class

function &instantiate_class(&$class_object)
{
	return $class_object;
}

//Loads the main config.php file

function &get_config()
{
	static $main_conf;

	if ( ! isset($main_conf))
	{
		if ( ! file_exists(APPPATH.'config/config'.EXT))
		{
			exit('The configuration file does not exist.');
		}

		include_once(APPPATH.'config/config'.EXT);

		if ( ! isset($config) OR ! is_array($config))
		{
			exit('Your config file does not appear to be formatted correctly.');
		}

		$main_conf[0] =& $config;
	}
	return $main_conf[0];
}

//Gets a config item

function config_item($item)
{
	static $config_item = array();

	if ( ! isset($config_item[$item]))
	{
		$config =& get_config();

		if ( ! isset($config[$item]))
		{
			return FALSE;
		}
		$config_item[$item] = $config[$item];
	}

	return $config_item[$item];
}


//Error Handler
if(function_exists('ssp_show_error')){
	//die();
}else{
	function ssp_show_error($message, $status_code = 200){

	}
}

function log_message($level = 'error', $message, $php_error = FALSE)
{
	static $LOG;
	
	$config =& get_config();
	if ($config['log_threshold'] == 0)
	{
		return;
	}

	$LOG =& load_class('Log');
	$LOG->write_log($level, $message, $php_error);
}

/**
 * Set HTTP Status Header
 *
 * @access	public
 * @param	int 	the status code
 * @param	string	
 * @return	void
 */
function set_status_header($code = 200, $text = '')
{

}

//Exception Handler


function _exception_handler($severity, $message, $filepath, $line)
{
	
}


//Determines if a string is comprised only of digits

if ( ! function_exists('ctype_digit'))
{
	function ctype_digit($str)
	{
		if ( ! is_string($str) OR $str == '')
		{
			return FALSE;
		}
		
		return ! preg_match('/[^0-9]/', $str);
	}	
}


//Determines if a string is comprised of only alphanumeric characters

if ( ! function_exists('ctype_alnum'))
{
	function ctype_alnum($str)
	{
		if ( ! is_string($str) OR $str == '')
		{
			return FALSE;
		}
		
		return ! preg_match('/[^0-9a-z]/i', $str);
	}	
}

if ( ! is_php('5.3'))
{
	@set_magic_quotes_runtime(0); // Kill magic quotes
}


/*
 * ------------------------------------------------------
 *  Instantiate the base classes
 * ------------------------------------------------------
 */

$CFG 	=& load_class('Config');
$OUT 	=& load_class('Output');
$IN		=& load_class('Input');
$LANG	=& load_class('Language');

if ( ! is_php('5.0.0'))
{
	log_message('debug', 'Not PHP 5 Loading Base4' );
	load_class('Loader', FALSE);
	include(BASEPATH.'includes/Base4'.EXT);
}
else
{
	log_message( 'debug', 'PHP 5 Or Above Loading Base5' );
	include(BASEPATH.'includes/Base5'.EXT);
}

// Load the base controller class
load_class('Controller', FALSE);


include( APPPATH.'controllers/SlideShowPro.php' );

/*
 * ------------------------------------------------------
 *  Security check
 * ------------------------------------------------------
 *
 *  None of the functions in the app controller or the
 *  loader class can be called via the URI, nor can
 *  controller functions that begin with an underscore
 */
$class  = 'SlideShowPro';

$SP = new $class();

$OUT->_display();

/* End of file CodeIgniter.php */
/* Location: ./system/codeigniter/CodeIgniter.php */