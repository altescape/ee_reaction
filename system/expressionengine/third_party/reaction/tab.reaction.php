<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Download_tab {

    function publish_tabs($channel_id, $entry_id = '')
    {
        $settings = array();
        $selected = array();
        $existing_files = array();

        $query = ee()->db->get('download_files');

        foreach ($query->result() as $row)
        {
            $existing_files[$row->file_id] = $row->file_name;
        }

        if ($entry_id != '')
        {
            $query = ee()->db->get_where('download_posts', array('entry_id' => $entry_id));

            foreach ($query->result() as $row)
            {
                $selected[] = $row->file_id;
            }
        }

        $id_instructions = lang('id_field_instructions');

        // Load the module lang file for the field label
        ee()->lang->loadfile('download');

        $settings[] = array(
            'field_id'      => 'download_field_ids',
            'field_label'       => lang('download_files'),
            'field_required'    => 'n',
            'field_data'        => $selected,
            'field_list_items'  => $existing_files,
            'field_fmt'     => '',
            'field_instructions'    => $id_instructions,
            'field_show_fmt'    => 'n',
            'field_pre_populate'    => 'n',
            'field_text_direction'  => 'ltr',
            'field_type'        => 'multi_select'
        );

        return $settings;
    }
}