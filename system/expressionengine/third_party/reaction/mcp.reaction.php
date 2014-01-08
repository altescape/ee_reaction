<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
// ------------------------------------------------------------------------

/**
 * MW Reactions Module Control Panel File
 *
 * @package    ExpressionEngine
 * @subpackage  Addons
 * @category  Module
 * @author    Michael Watts
 * @link    http://michaelwatts.me
 * @copyright   Copyright (c) 2013 Michael Watts
 * @license     http://creativecommons.org/licenses/by-sa/3.0/ Attribution-Share Alike 3.0 Unported
 */
 
// ------------------------------------------------------------------------

class Reaction_mcp {
	
	public $return_data;
	
	private $_base_url;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
        $this->EE =& get_instance();
	}
	
	// ----------------------------------------------------------------

	/**
	 * Index Function
	 *
	 * @return 	void
	 */
	public function index()
	{
        $this->EE->cp->set_variable('cp_page_title', lang('reaction_module_name'));
		
		/**
		 * No control panel page yet
		 **/		
	}
	
}
/* End of file mcp.vz_average.php */
/* Location: /system/expressionengine/third_party/reaction/mcp.reaction.php */