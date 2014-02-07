<?php  if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

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

class Reaction_mcp
{

    public $return_data;

    private $_base_url;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->EE =& get_instance();

        $this->_base_url	= 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=reaction';

        ee()->cp->set_right_nav(
            array(
                'Add Group' => BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=reaction'.AMP.'method=group_add',
                'Groups' => BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=reaction'.AMP.'method=list_groups'
            )
        );
    }

    // ----------------------------------------------------------------

    /**
     * Index Function
     *
     * @return  void
     */
    public function index()
    {

        ee()->load->library(array('table','javascript'));
        $this->EE->cp->set_variable('cp_page_title', lang('reaction_module_name'));
        ee()->view->cp_page_title = lang('reaction_module_name');
        ee()->cp->add_to_head('<style type="text/css" media="screen"></style>');

        $vars = array(
            'cp_page_title'				=> lang('reaction_module_name'),
            'module_base'				=> $this->_base_url,
            'action'					=> $this->_base_url.AMP.'method=reaction_update'
        );
        $message = ee()->session->flashdata('result_message');
        return ee()->load->view('index', $vars, TRUE);

    }

    public function group_add()
    {
        ee()->load->library(array('table','javascript'));
        $this->EE->cp->set_variable('cp_page_title', lang('reaction_module_name'));
        ee()->view->cp_page_title = lang('reaction_module_name');
        ee()->cp->add_to_head('<style type="text/css" media="screen"></style>');

        $vars = array(
            'cp_page_title'				=> lang('reaction_module_name'),
            'module_base'				=> $this->_base_url,
            'action'					=> $this->_base_url.AMP.'method=save'
        );

        return ee()->load->view('group_add', $vars, TRUE);

    }

    public function save()
    {
        ee()->load->library(array('table','javascript'));
        $this->EE->cp->set_variable('cp_page_title', lang('reaction_module_name'));
        ee()->view->cp_page_title = lang('reaction_module_name');
        ee()->cp->add_to_head('<style type="text/css" media="screen"></style>');

        $group_name = $_POST['group_name'];
        $reaction_names = array(
            'reaction_1' => $_POST['r1'],
            'reaction_2' => $_POST['r2'],
            'reaction_3' => $_POST['r3'],
            'reaction_4' => $_POST['r4'],
            'reaction_5' => $_POST['r5'],
        );
        $reaction_names_serial = serialize($reaction_names);

        // put into database
        ee()->db->insert(
            'reaction_groups',
            array(
                'reaction_group_name'  => $group_name,
                'reactions' => $reaction_names_serial,
            )
        );


        $vars = array(
            'data' => $_POST,
            'group_name' => $group_name,
            'reaction_names' => $reaction_names,
            'reaction_names_serial' => $reaction_names_serial
        );

        return ee()->load->view('group_save', $vars, TRUE);
    }

    /**
     * @return array
     */
    public function records()
    {
        if (func_num_args() >= 1) {
            // Query db with id
            $results = ee()->db->select('*')
                ->from('reaction_groups')
                ->where(array(
                        'reaction_group_id' => func_get_arg(0)
                    ))
                ->get();
        } else {
            // Query db without id
            $results = ee()->db->select('*')
                ->from('reaction_groups')
                ->get();

        }

        if ($results->num_rows() == 0)
        {
            exit('No groups exist');
        }

        $data = array();

        if ($results->num_rows() > 0)
        {
            foreach($results->result_array() as $row)
            {
                $reactions = array(
                    'reaction_group_id' => $row['reaction_group_id'],
                    'reaction_group_name' => $row['reaction_group_name'],
                    'reactions' => $row['reactions'],
                );
                array_push($data, $reactions);
            }
        }

        return $data;
    }

    public function list_groups()
    {
        ee()->load->library(array('table','javascript'));
        $this->EE->cp->set_variable('cp_page_title', lang('reaction_module_name'));
        ee()->view->cp_page_title = lang('reaction_module_name');
        ee()->cp->add_to_head('<style type="text/css" media="screen"></style>');

        $vars = array(
            'cp_page_title'				=> lang('reaction_module_name'),
            'data' => $this->records(),
        );

        return ee()->load->view('group_list', $vars, TRUE);

    }

    public function record()
    {
        ee()->load->library(array('table','javascript'));
        $this->EE->cp->set_variable('cp_page_title', lang('reaction_module_name'));
        ee()->view->cp_page_title = lang('reaction_module_name');
        ee()->cp->add_to_head('<style type="text/css" media="screen"></style>');

        $vars = array(
            'cp_page_title'				=> lang('reaction_module_name'),
            'module_base'				=> $this->_base_url,
            'action'					=> $this->_base_url.AMP.'method=update',
            'data' => $this->records($_GET['id'])
        );

        return ee()->load->view('group_edit', $vars, TRUE);

    }

    public function update()
    {
        ee()->load->library(array('table','javascript'));
        $this->EE->cp->set_variable('cp_page_title', lang('reaction_module_name'));
        ee()->view->cp_page_title = lang('reaction_module_name');
        ee()->cp->add_to_head('<style type="text/css" media="screen"></style>');

        $id = $_POST['id'];
        $group_name = $_POST['group_name'];
        $reaction_names = array(
            'reaction_1' => $_POST['r1'],
            'reaction_2' => $_POST['r2'],
            'reaction_3' => $_POST['r3'],
            'reaction_4' => $_POST['r4'],
            'reaction_5' => $_POST['r5'],
        );
        $reaction_names_serial = serialize($reaction_names);

        // update
        ee()->db->update(
            'reaction_groups',
            array(
                'reaction_group_name'  => $group_name,
                'reactions' => $reaction_names_serial,
            ),
            array(
                'reaction_group_id' => $id
            )
        );

        return ee()->load->view('group_list', $this->list_groups(), TRUE);

    }

}
/* End of file mcp.reaction.php */
/* Location: /system/expressionengine/third_party/reaction/mcp.reaction.php */