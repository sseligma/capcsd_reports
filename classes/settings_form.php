<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

/**
 * Event list filter form.
 *
 * @package    report_capcsd
 * @copyright  2017 onwards Seth Seligman
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_capcsd_settings_form extends moodleform {

    /**
     * Form definition method.
     */
    public function definition() {
    	    	
    	$report_types = array(
    	  'asha' => 'ASHA',
    	  'aaa' => 'AAA',
    	  'general' => 'General'
    	);
    	    	
        $module_options = $this->get_module_options();
        
        $start_date = new DateTime('first day of this month');
        
    	$mform = $this->_form; // Don't forget the underscore!
    	
    	$mform->addElement('header', 'displayinfo', 'Report Settings');    	
    	$mform->addElement('select', 'report_type', 'Report Type', $report_types);
    	$mform->addElement('date_selector', 'start_date', get_string('start_date','report_capcsd'));
    	$mform->setDefault('start_date', $start_date->getTimestamp());
    	$mform->addElement('date_selector', 'end_date', get_string('end_date','report_capcsd'));
    	$mform->addElement('text', 'pass_fail_percentage', get_string('pass_fail_percentage', 'report_capcsd'));
    	$mform->addElement('select', 'quiz_id', get_string('quiz_id', 'report_capcsd'), $module_options);
    	$mform->addElement('text', 'asha_course_number', get_string('asha_course_number', 'report_capcsd'));
    	$mform->addElement('text', 'aaa_course_code', get_string('aaa_course_code', 'report_capcsd'));
    	$mform->addElement('text', 'ceus_approved', get_string('ceus_approved', 'report_capcsd'));
    	$mform->addElement('submit', 'submitbutton', get_string('submit','report_capcsd'));
    	    	
    }
    
    function validation($data, $files) {
      //echo var_export($data);
      $errors= array();

      
      if ($data['report_type'] != 'general' && $data['quiz_id'] == '_none') {
      	$errors['quiz_id'] = 'Module is required for AAA and ASHA reports';      	
      }
      	
      return $errors;
    }
    
    public function get_course_options() {
      global $DB;
      $options = array('_none' => '--course--');
      
      $q = "
      select 
      c.id,
      c.fullname,
      c.shortname 
      from 
      {course} c
      order by
      c.fullname";
      
      $result = $DB->get_records_sql($q);
      
      foreach ($result as $row) {
      	$options[strval($row->id)] = $row->fullname;
      }
      
      return $options;
    }
    
    public function get_module_options() {
    	global $DB;
    	$options = array('_none' => '--module--');
  
    	$q = "
        select
        concat(c.shortname,' - ',s.name) as module,
        m.instance as quiz_id
        from
        {course} c
        join {course_sections} s on c.id = s.course
        join {course_modules} m on c.id = m.course and s.id = m.section
        where
        m.module = 16
        order by 
        c.shortname,
        s.section";
    	
    	$result = $DB->get_records_sql($q);
    	
    	foreach ($result as $row) {
    		$options[strval($row->quiz_id)] = $row->module;
    	}
    	
    	return $options;
    }
}
