<?php  if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

// ------------------------------------------------------------------------

/**
 * MW Reactions Module Install/Update File
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

class Reaction_upd
{

    public $version = '0.0.3';

    private $EE;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->EE =& get_instance();
    }

    // ----------------------------------------------------------------

    /**
     * Installation Method
     *
     * @return    boolean    TRUE
     */
    public function install()
    {
        $mod_data = array(
            'module_name' => 'Reaction',
            'module_version' => $this->version,
            'has_cp_backend' => "y",
            'has_publish_fields' => 'n'
        );
        $this->EE->db->insert('modules', $mod_data);

        # Add an action for the AJAX update
        $data = array(
            'class' => 'Reaction',
            'method' => 'react'
        );
        $this->EE->db->insert('actions', $data);

        // Reaction 'reactions' table
        $this->EE->load->dbforge();
        $fields = array(
            'value' => array(
                'type' => 'varchar',
                'constraint' => 255
            ),
            'reaction_group_id' => array(
                'type' => 'int',
                'constraint' => '10',
                'default' => '0'
            ),
            'entry_id' => array(
                'type' => 'int',
                'constraint' => 5,
                'unsigned' => true
            ),
            'url_title' => array(
                'type' => 'varchar',
                'constraint' => 255
            ),
            'site_id' => array(
                'type' => 'int',
                'constraint' => 5,
                'unsigned' => true
            ),
            'date' => array(
                'type' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
            ),
            'user_id' => array(
                'type' => 'int',
                'constraint' => 5,
                'unsigned' => true,
                'null' => true
            ),
            'ip' => array(
                'type' => 'varchar',
                'constraint' => 15,
                'null' => true
            )
        );
        $this->EE->dbforge->add_field('id');
        $this->EE->dbforge->add_field($fields);
        $this->EE->dbforge->add_key('entry_id', true);
        $this->EE->dbforge->add_key('reaction_group_id', true);
        $this->EE->dbforge->create_table('reaction');

        // Reactions admin build table
        $fields = array(
            'reaction_group_id' => array(
                'type' => 'int',
                'constraint' => '10',
                'unsigned' => true,
                'auto_increment' => true
            ),
            'reaction_group_name' => array(
                'type' => 'varchar',
                'constraint' => '100'
            ),
            'reactions' => array(
                'type' => 'varchar',
                'constraint' => '255'
            )
        );
        ee()->dbforge->add_field($fields);
        ee()->dbforge->add_key('reaction_group_id', true);
        ee()->dbforge->create_table('reaction_groups');

        return true;
    }

    // ----------------------------------------------------------------

    /**
     * Uninstall
     *
     * @return    boolean    TRUE
     */
    public function uninstall()
    {
        $mod_id = $this->EE->db->select('module_id')
            ->get_where(
                'modules',
                array(
                    'module_name' => 'Reaction'
                )
            )->row('module_id');

        $this->EE->db->where('module_id', $mod_id)
            ->delete('module_member_groups');

        $this->EE->db->where('module_name', 'Reaction')
            ->delete('modules');

        // Remove our custom action
        $this->EE->db->where('class', 'Reaction');
        $this->EE->db->delete('actions');

        // Remove the data tables
        $this->EE->load->dbforge();
        $this->EE->dbforge->drop_table('reaction');
        $this->EE->dbforge->drop_table('reaction_groups');

        return true;
    }

    // ----------------------------------------------------------------

    /**
     * @param string $current
     * @return bool
     */
    public function update($current = '')
    {
        if ($this->version == $current) {
            return false;
        }

        $this->EE->load->dbforge();

        if (version_compare($current, '0.0.3', '<')) {
            $this->_update_060();
        }

        return true;
    }

    /**
     * Add 'site_id' database column
     */
    private function _update_060()
    {
        // Reaction user set reactions table
        $fields = array(
            'reaction_group_id' => array(
                'type' => 'int',
                'constraint' => '10',
                'unsigned' => true,
                'auto_increment' => true
            ),
            'reaction_group_name' => array(
                'type' => 'varchar',
                'constraint' => '100'
            ),
            'reactions' => array(
                'type' => 'varchar',
                'constraint' => '255'
            )
        );
        ee()->dbforge->add_field($fields);
        ee()->dbforge->add_key('reaction_group_id', true);
        ee()->dbforge->create_table('reaction_groups');
    }

}
/* End of file upd.reaction.php */
/* Location: /system/expressionengine/third_party/reaction/upd.reaction.php */