<?php  if (!defined('BASEPATH')) {
  exit('No direct script access allowed');
}

// ------------------------------------------------------------------------

/**
 * MW Reactions Front End File
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

class Reaction
{

  public $return_data;
  public $reactions_number = 5;

  /**
   * Constructor
   */
  public function __construct()
  {
    $this->EE =& get_instance();
  }

  // ----------------------------------------------------------------



  // ----------------------------------------------------------------

  /**
   * Forms tag
   * Builds the form
   *
   * @return string
   */

  public function form()
  {
    /*
     * Build form
     */
    $action = $this->EE->functions->fetch_site_index(
        0,
        0
      ) . QUERY_MARKER . 'ACT=' . $this->EE->functions->fetch_action_id('reaction', 'react');

    $form_details = array(
      'action' => $action,
      'secure' => true
    );

    if ($this->EE->TMPL->fetch_param('entry_id', false)) {
      $form_details['hidden_fields']['entry_id'] = $this->EE->TMPL->fetch_param('entry_id');
    } else {
      return '<!-- You must specify an entry_id for the rating form. -->';
    }

    if ($this->EE->TMPL->fetch_param('reactions')) {
      $this->reactions = explode('|', $this->EE->TMPL->fetch_param('reactions'));
      $this->reactions_number = count($this->reactions);
    }

    $form_details['hidden_fields']['url_title'] = $this->EE->TMPL->fetch_param('url_title', 'url_title');
    $form_details['hidden_fields']['site_id'] = $this->EE->TMPL->fetch_param('site_id', '1');

    // Form parameters
    $form_details['id'] = $this->EE->TMPL->fetch_param('form_id');
    $form_details['class'] = $this->EE->TMPL->fetch_param('form_class');

    // Encode a bunch of variables we'll need on the other end
    $settings['return'] = $this->EE->TMPL->fetch_param('return');
    $settings['secure_return'] = $this->EE->TMPL->fetch_param('secure_return');
    $settings['limit_by'] = $this->EE->TMPL->fetch_param('limit_by');
    $settings['update_field'] = $this->EE->TMPL->fetch_param('update_field');
    $settings['update_with'] = $this->EE->TMPL->fetch_param('update_with');
    $form_details['hidden_fields']['form_settings'] = base64_encode(serialize($settings));

    // Generate the <form> tags
    $return = $this->EE->functions->form_declaration($form_details);
    $section = '';


    /*
     * Build output
     */
    for ($i = 0; $i < $this->reactions_number; $i++) {
      $section .= $this->EE->TMPL->tagdata;
      $section = str_replace('{reaction}', $this->reactions[$i], $section);
      $section = str_replace('{id}', $i, $section);
      $section = str_replace('{num}', $i, $section);
      $section = str_replace('{reaction_count}', $this->_data_array($i), $section);
    }
    $return .= $section;

    $return .= '</form>';

    return $return;
  }

  // ----------------------------------------------------------------
  /**
   * Total_votes tag
   * Total number of votes for entry
   * @return int
   */
  public function total_votes()
  {
    $data = $this->_get_data();
    return count($data);
  }

  // ----------------------------------------------------------------

  /**
   * Handle the action url for rating and entry
   */
  public function react()
  {
    // Validate our data
    $entry_id = $this->EE->input->post('entry_id');
    if (!empty($entry_id) && ctype_digit($entry_id)) {
      $entry_id = intval($entry_id, 10);
    } else {
      exit('Error: You must supply a valid entry id.');
    }

    $value = $this->EE->input->post('value');
    if (is_numeric($value)) {
      $value = intval($value, 10);
    } else {
      exit('Error: You must supply a numeric rating value.');
    }

    // Differentiate between duplicate IDs
    $url_title = $this->EE->input->post('url_title');
    $site_id = $this->EE->input->post('site_id');

    // Make sure it is a valid POST from the front-end
    // Mike: this throws an error
//        if ($this->EE->security->check_xid($this->EE->input->post('XID')) == FALSE)
//        {
//        	// No data insertion if a hash isn't found or is too old
//        	$this->functions->redirect($this->EE->functions->form_backtrack());
//        }

    // Decode the form settings
    $settings = unserialize(base64_decode($this->EE->input->post('form_settings')));

    // Store information about the user, to prevent duplicates
    $user_id = $this->EE->session->userdata('member_id');
    $ip = $this->EE->input->ip_address();

    // If limited by member_id, make sure someone is logged in
    if ($settings['limit_by'] == 'member' && !$user_id) {
      exit('Error: You must be logged in to react!');
    }

    // Prevent duplicate votes, if needed
    if ($settings['limit_by'] == 'ip') {
      // Delete any previous votes from this IP
      $this->EE->db->delete('exp_reaction', array('ip' => $ip, 'entry_id' => $entry_id));
    } else {
      if ($settings['limit_by'] == 'member') {
        $this->EE->db->delete('exp_reaction', array('user_id' => $user_id, 'entry_id' => $entry_id));
      }
    }

    // Prepare the row for our database
    $data = array(
      'value' => $value,
      'entry_id' => $entry_id,
      'url_title' => $url_title,
      'site_id' => $site_id,
      'user_id' => $user_id,
      'ip' => $ip
    );

    // Create the new row
    $sql = $this->EE->db->insert_string('exp_reaction', $data);
    $this->EE->db->query($sql);

    // Recalculate the cumulative data
    $this->EE->db->where('entry_id', $entry_id);
    $this->EE->db->where('url_title', $url_title);
    $this->EE->db->where('site_id', $site_id);

    $this->EE->db->select('COUNT(`value`) AS count');
    $query = $this->EE->db->get('exp_reaction');
    $cumulative = $query->row_array();

    // Do we need to update a custom field?
    if ($url_title == 'channel' && $settings['update_field']) {
      // Get the field ID
      $this->EE->db->select('field_id');
      $query = $this->EE->db->get_where(
        'exp_channel_fields',
        array(
          'field_name' => $settings['update_field'],
          'site_id' => $this->EE->input->post('site_id')
        ),
        1
      );

      // If that field exists….
      if ($query->num_rows() > 0) {
        $row = $query->row();
        $field_id = $row->field_id;

        $type = in_array($settings['update_with'], array('count')) ? $settings['update_with'] : 'average';

        // Update the field
        $this->EE->db->update(
          'exp_channel_data',
          array('field_id_' . $field_id => $cumulative[$type]),
          array('entry_id' => $entry_id, 'site_id' => $this->EE->input->post('site_id'))
        );
      }
    }

    // Okay, now get ready to send back a response
    if (AJAX_REQUEST) {
      // Ajax call, send back data they can use
      exit(json_encode($cumulative));
    } else {
      // Redirect to the specified page
      $redirect = !empty($settings['return']) ?
        $this->EE->functions->create_url($settings['return']) :
        $this->EE->functions->form_backtrack();

      // Use the https version if they set 'secure_return'
      if ($settings['secure_return'] == 'yes') {
        $redirect = str_replace('http://', 'https://', $redirect);
      }

      $this->EE->functions->redirect($redirect);
    }

    return;
  }

  // ----------------------------------------------------------------

  /**
   * Output the number of ratings for the current entry
   * @return mixed
   */
  public function reaction_data()
  {
    $data = $this->_get_data();
    return $data;
  }

  public function active() // this is meant get the current member and see what the voted for
  {
    // get value
    if ($this->EE->TMPL->fetch_param('value')) {
      $value = $this->EE->TMPL->fetch_param('value');
    } else {
      return;
    }
    // get entry id
    if ($this->EE->TMPL->fetch_param('entry_id')) {
      $entry_id = $this->EE->TMPL->fetch_param('entry_id');

    } else {
      return;
    }

    // if logged in
    if ($this->EE->session->userdata('member_id')) {
      // get member ID
      $member_id = $this->EE->session->userdata('member_id');

      $this->EE->db->where('user_id', $member_id);
      $this->EE->db->where('entry_id', $entry_id);
      $this->EE->db->where('value', $value);
    } else {
      // get the current users IP
      $ip = $this->EE->input->ip_address();

      // check the db for this IP
      $this->EE->db->where('ip', $ip);
    }

    // if found return css class: active or whatever set by param maybe
    $query = $this->EE->db->get('exp_reaction');
    if (isset($query)) {
      return "active";
    }
    return;
  }

  // ----------------------------------------------------------------

  /**
   * Add up the ratings and return an average
   * @return array|string
   */
  private function _get_total() // can delete, but not until grabbed member logic
  {
    // Make sure we don't try to run this when we don't have access to the template
    if (isset($this->EE->TMPL)) {

      // Must specify the entry id
      if ($this->EE->TMPL->fetch_param('entry_id')) {

        // Fetch params
        $entry_id = $this->EE->TMPL->fetch_param('entry_id');
        $site_id = $this->EE->TMPL->fetch_param('site_id', '1');
        $limit_by = $this->EE->TMPL->fetch_param('current_by');
        $member_id = $this->EE->TMPL->fetch_param('member_id');

        // Run the DB query
        $this->EE->db->where('entry_id', $entry_id);
        $this->EE->db->where('site_id', $site_id);

        // Limit to just ratings from one user, if necessary
        if ($limit_by == 'ip') {
          $ip = $this->EE->input->ip_address();
          $this->EE->db->where('ip', $ip);
        } elseif ($limit_by == 'member') {

          // Make sure there IS a logged in member
          if (!isset($this->EE->session)) {
            return array('total' => 0);
          }

          $member_id = $this->EE->session->userdata('member_id');
          $this->EE->db->where('user_id', $member_id);
        } elseif ($member_id) {
          $this->EE->db->where('user_id', $member_id);
        }

        // Get all the other information
        $this->EE->db->select('COUNT(`value`) AS total');
        $query = $this->EE->db->get('exp_reaction');

        $data = $query->row_array();
        return $data;
      } else {
        return;
      }

    }

  }

  private function _get_data()
  {
    if (!isset($this->EE->TMPL)) { return; }

    if (!ee()->session->cache('super_class', 'reaction_total')) {

      // Check for params
      if (!$this->EE->TMPL->fetch_param('entry_id')) { return; } // stop if no params

      // Fetch params
      $entry_id = $this->EE->TMPL->fetch_param('entry_id');

      // DB query
      $this->EE->db->where('entry_id', $entry_id);
      $query = $this->EE->db->get('exp_reaction');

      // Cycle through query
      if ($query->num_rows() > 0) {
        $counts = array();
        foreach ($query->result_array() as $row) {
          $counts[] = $row;
        }
        ee()->session->set_cache('super_class', 'reaction_total', $counts);
      }
    }
    $totals = ee()->session->cache('super_class', 'reaction_total');
    return $totals;
  }

  /**
   * Builds array from data
   *
   * @param $idx
   * @return mixed
   */

  private function _data_array($idx)
  {
    /*
     * Get totals for each function
     */
    $reaction_data = $this->reaction_data();
    $each_reaction_total = array();
    $reaction_sum_empty = array();
    $reaction_sum = array();
    $prefilled_array = array();

    // check if any values are set yet
    if(empty($reaction_data)){ // EMPTY
      // prebuild an array with 0 for all values
      for ($i = 0; $i < $this->reactions_number; $i++) {
        $prefilled_array[$i] = 0; // pre fill the array
      }
    } else { // NOT EMPTY
      for ($i = 0; $i < count($reaction_data); $i++) {
        $each_reaction_total[$i] = $reaction_data[$i]['value'];
      }
      // prebuild an array with 0 for all values
      for ($j = 0; $j < $this->reactions_number; $j++) {
        $reaction_sum_empty[$j] = 0; // pre fill the array
      }
      // get totals of each reaction count
      if (count($each_reaction_total) > 0){
        $reaction_sum = array_count_values($each_reaction_total);
      }
      // cycle through empty prefilled array and fill with real results
      foreach ($reaction_sum_empty as $real_key => $empty_val ) {
        $prefilled_array[$real_key] = $reaction_sum_empty[$real_key];
        foreach($reaction_sum as $a_key => $real_val ) {
          if (isset($reaction_sum[$real_key])) {
            $prefilled_array[$real_key] = $reaction_sum[$real_key];
          } else {
            $prefilled_array[$real_key] = 0;
          }
        }
      }
    };

    return $prefilled_array[$idx];
  }


}
/* End of file mod.reaction.php */
/* Location: /system/expressionengine/third_party/reaction/mod.reaction.php */