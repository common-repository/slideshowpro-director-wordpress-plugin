<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2009, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Application Controller Class
 *
 * This class object is the super class that every library in
 * CodeIgniter will be assigned to.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/general/controllers.html
 */
class Controller extends SP_Base{

	var $_ci_scaffolding	= FALSE;
	var $_ci_scaff_table	= FALSE;
	
	/**
	 * Constructor
	 *
	 * Calls the initialize() function
	 */
	function Controller()
	{	
		parent::SP_Base();
		$this->_ci_initialize();
		log_message('debug', "Controller Class Initialized");
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize
	 *
	 * Assigns all the bases classes loaded by the front controller to
	 * variables in this class.  Also calls the autoload routine.
	 *
	 * @access	private
	 * @return	void
	 */
	function _ci_initialize()
	{
		// Assign all the class objects that were instantiated by the
		// front controller to local class variables so that CI can be
		// run as one big super object.
		$classes = array(
							'config'	=> 'Config',
							'input'		=> 'Input',
							'output'	=> 'Output',
							'lang'		=> 'Language'
							);
		
		foreach ($classes as $var => $class)
		{
			$this->$var =& load_class($class);
		}
		

		// In PHP 5 the Loader class is run as a discreet
		// class.  In PHP 4 it extends the Controller
		if (floor(phpversion()) >= 5)
		{
			$this->load =& load_class('Loader');
			$this->load->_ci_autoloader();
		}
		else
		{
			$this->_ci_autoloader();
			
			// sync up the objects since PHP4 was working from a copy
			foreach (array_keys(get_object_vars($this)) as $attribute)
			{
				if (is_object($this->$attribute))
				{
					$this->load->$attribute =& $this->$attribute;
				}
			}
		}
	}
}
// END _Controller class

/* End of file Controller.php */
/* Location: ./system/libraries/Controller.php */