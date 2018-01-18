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

/**
 * capcsd documentation.
 *
 * @package    report_capcsd
 * @copyright  2017 onwards Seth Seligman
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/classes/settings_form.php');
require_once(__DIR__ . '/classes/reports.php');
require_once($CFG->libdir . '/adminlib.php');

require_login();
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_pagelayout('report');

$format = isset($_GET['format'])?$_GET['format']:'table';

if ($format == 'table') {
	
  $mform = new report_capcsd_settings_form();

  //Form processing and displaying is done here
  if ($mform->is_cancelled()) {
	//Handle form cancel operation, if cancel button is present on form
  } else if ($formdata = $mform->get_data()) {
	$report = new report_capcsd($formdata);
	$table = $report->render_report();
	
	$params = $formdata;
	$asha_report_url = new moodle_url('/report/capcsd/index.php',  array_merge((array)$params,array('format' => 'xls','report_name' => 'asha_report')));
	$asha_reference_report_url = new moodle_url('/report/capcsd/index.php', array_merge((array)$params,array('format' => 'xls','report_name' => 'asha_reference_report')));
	$aaa_report_url = new moodle_url('/report/capcsd/index.php', array_merge((array)$params,array('format' => 'xls','report_name' => 'aaa_report')));
	$general_report_url = new moodle_url('/report/capcsd/index.php', array_merge((array)$params,array('format' => 'xls','report_name' => 'general_report')));
	//In this case you process validated data. $mform->get_data() returns data posted in form.
  } 
  else {
	// this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
	// or on the first display of the form.
	
	//Set default data (if any)
	//$mform->set_data($toform);
	//displays the form
	//$mform->display();
  }


  admin_externalpage_setup('reportcapcsd', '', null, '', array('pagelayout'=>'report'));
  echo $OUTPUT->header();

  echo $OUTPUT->heading(get_string('capcsd', 'report_capcsd'));
		
  $mform->display();

  if (isset($table)) {
    echo html_writer::table($table);

    $attr = array('target' => '_blank');
    echo '<div>' . html_writer::link($asha_report_url,'ASHA Report XLS',$attr) . '</div>';
    echo '<div>' . html_writer::link($asha_reference_report_url,'ASHA Reference Report XLS',$attr) . '</div>';
    echo '<div>' . html_writer::link($aaa_report_url,'AAA Report XLS',$attr) . '</div>';
    echo '<div>' . html_writer::link($general_report_url,'General Report XLS',$attr) . '</div>';
  
  }

  echo $OUTPUT->footer();
}
else {  	
  $report = new report_capcsd();
  $report->get_excel();
}