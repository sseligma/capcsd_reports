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
 * Log report renderer.
 *
 * @package    report_log
 * @copyright  2014 Rajesh Taneja <rajesh.taneja@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot.'/lib/excellib.class.php');

/**
 * Report log renderer's for printing reports.
 *
 * @package    report_log
 * @copyright  2014 Rajesh Taneja <rajesh.taneja@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_capcsd {

  var $report_types;
  var $report_type;
  var $report_name;
  var $start_date;
  var $end_date;
  var $pass_fail_percentage;
  var $quiz_id;
  var $asha_course_number;
  var $aaa_course_code;
  var $ceus_approved;
  
  private function get_params() {
  	$params = new stdClass();
  	if (isset($_GET['report_type'])) {
  	  $params->report_type = $_GET['report_type'];
  	}
  	
  	if (isset($_GET['report_name'])) {
  	  $params->report_name = $_GET['report_name'];
  	}
  	
  	if (isset($_GET['start_date'])) {
  		$params->start_date = $_GET['start_date'];
  	}
  	
  	if (isset($_GET['end_date'])) {
  		$params->end_date = $_GET['end_date'];
  	}
  	
  	if (isset($_GET['pass_fail_percentage'])) {
  		$params->pass_fail_percentage = $_GET['pass_fail_percentage'];
  	}
  	
  	if (isset($_GET['quiz_id'])) {
  		$params->quiz_id = $_GET['quiz_id'];
  	}
  	  	
  	if (isset($_GET['asha_course_number'])) {
  		$params->asha_course_number = $_GET['asha_course_number'];
  	}
  	
  	if (isset($_GET['aaa_course_code'])) {
  		$params->aaa_course_code = $_GET['aaa_course_code'];
  	}
  	
  	if (isset($_GET['ceus_approved'])) {
  		$params->ceus_approved = $_GET['ceus_approved'];
  	}
  	  	
  	return $params;  	
  }
  
  function __construct($options = null) {
  	
  	if (!$options) {
  	  $options = $this->get_params();	
  	}
  	
  	if (isset($options->report_types)) {
  	  $this->report_types = $options->report_types;
  	}
  	
    if (isset($options->report_type)) {	
      $this->report_type = $options->report_type;
    }
    
    if (isset($options->report_name)) {
      $this->report_name = $options->report_name;
    }
    
    if (isset($options->start_date)) {
      $this->start_date = $options->start_date;
    }
    
    if (isset($options->end_date)) {
      $this->end_date = $options->end_date;
    }
    
    if (isset($options->pass_fail_percentage)) {
      $this->pass_fail_percentage = $options->pass_fail_percentage;
    }
    
    if (isset($options->quiz_id)) {
    	$this->quiz_id = $options->quiz_id;
    }
    
    if (isset($options->asha_course_number)) {
      $this->asha_course_number = $options->asha_course_number;
    }
    
    if (isset($options->aaa_course_code)) {
    	$this->aaa_course_code = $options->aaa_course_code;
    }
    
    if (isset($options->ceus_approved)) {
    	$this->ceus_approved = $options->ceus_approved;
    }
        
  }
  
  public function render_report() {
  	$type = $this->report_type;
    $data = $this->get_report_data();
    $table = new html_table();
 
    switch ($type) {
      case 'asha':
      	$table->head = array('Customer Name','ASHA ID','Address1','Address2','Address3','City','State','ZIP','Country','Primary Phone','Email','Grade','Date');
      	
        foreach ($data as $d) {
        	$table->data[] = array($d->lastname . ' ' . $d->firstname, $d->asha, $d->address1, $d->address2, $d->address3,$d->city, $d->state, $d->zip, $d->country, $d->phone1,$d->email,$d->grade_percentage,$d->time_completed);
        }
        return $table;
      break;	
      
      case 'aaa':
      	$table->head = array('Customer Name','AAA ID','Address1','Address2','Address3','City','State','ZIP','Country','Primary Phone','Email','Grade','Date');
      	
      	foreach ($data as $d) {
      		$table->data[] = array($d->lastname . ' ' . $d->firstname, $d->aaa, $d->address1, $d->address2, $d->address3,$d->city, $d->state, $d->zip, $d->country, $d->phone1,$d->email,$d->grade_percentage,$d->time_completed);
      	}
      	return $table;
      break;
     
      case 'general':
      	$table->head = array('Customer Name','ASHA ID','AAA ID','Address1','Address2','Address3','City','State','ZIP','Country','Primary Phone','Email','Module','Grade','Date');
      	
      	foreach ($data as $d) {
      		$table->data[] = array($d->lastname . ' ' . $d->firstname, $d->asha, $d->aaa,$d->address1, $d->address2, $d->address3,$d->city, $d->state, $d->zip, $d->country, $d->phone1,$d->email,$d->module,$d->grade_percentage,$d->time_completed);
      	}
      	return $table;
      break;
    }
        
  }
  
  public function get_report_data() {
  	 global $DB;
  	 $data = array();
  	
  	 $type = $this->report_type;
  	 
  	 switch ($type) {
  	 	case 'asha':
  	 	  $query = $this->get_report_query($type);
  	 	  
  	 	  $result = $DB->get_records_sql($query['query'],$query['params']);
  	 	  
  	 	  foreach ($result as $row) {
  	 	  	$data[] = $row;
  	 	  }
  	 	break;
  	 	
  	 	case 'aaa':
  	 	  $query = $this->get_report_query($type);
  	 		
  	 	  $result = $DB->get_records_sql($query['query'],$query['params']);
  	 		
  	 	  foreach ($result as $row) {
  	 	    $data[] = $row;
  	 	  }
  	    break;
  	    
  	 	case 'general':
  	 	  $query = $this->get_report_query($type);
  	 		
  	 	  $result = $DB->get_records_sql($query['query'],$query['params']);
  	 		
  	 	  foreach ($result as $row) {
  	 		$data[] = $row;
  	 	  }
  	 	break;
  	 		
  	 }
  	   	 
  	 return $data;
  }
  
  public function get_report_query($type) {
  	$base_query = "
  	select
    concat(user.id,'_',q.id) as grade_key,
    user.id,
    user.firstname,
    user.lastname,
    user.username,
    user.email,
    user.phone1,
    user.address1,
    user.address2,
    user.address3,
    user.city,
    user.state,
    user.zip,
    user.country,
    user.asha,
    user.aaa,
    section.module,
    q.grade,
  	round(( (g.grade /q.grade) * 100),0) as grade_percentage,
    DATE_FORMAT(FROM_UNIXTIME(g.timemodified), '%m/%d/%Y') as time_completed
  	from
    {quiz} q
    left join (
      SELECT 
      g1.id,
      g1.quiz, 
      g1.userid, 
      g1.grade, 
      g1.timemodified
      FROM 
      {quiz_grades} g1
      INNER JOIN (
        SELECT 
        quiz,
        userid,
        MAX(timemodified) timemodified
        FROM 
        {quiz_grades}
        GROUP BY 
        quiz,
        userid
      ) g2 ON g1.quiz = g2.quiz AND g1.userid = g2.userid and g1.timemodified = g2.timemodified
    ) g on q.id = g.quiz
    left join 
    (
      select
      u.id,
      u.firstname,
      u.lastname,
      u.username,
      u.email,
      u.phone1,
      max(if(f.shortname = 'address1',d.data,'')) as address1,
      max(if(f.shortname = 'address2',d.data,'')) as address2,
      max(if(f.shortname = 'address3',d.data,'')) as address3,
      max(if(f.shortname = 'city',d.data,'')) as city,
      max(if(f.shortname = 'state',d.data,'')) as state,
      max(if(f.shortname = 'zip',d.data,'')) as zip,
      u.country,
      max(if(f.shortname = 'ASHA',d.data,'')) as asha,
      max(if(f.shortname = 'AAA',d.data,'')) as aaa
      from
      {user} u
      left join {user_info_data} d on u.id = d.userid
      left join {user_info_field} f on d.fieldid = f.id
      group by
      u.firstname,
      u.lastname,
      u.username,
      u.email,
      u.phone1,
      u.country
      order by
      u.lastname,
      u.firstname
    ) user on g.userid = user.id
    left join 
    (
      select
      s.name as module,
      m.instance as quiz_id
      from
      {course} c
      join {course_sections} s on c.id = s.course
      join {course_modules} m on c.id = m.course and s.id = m.section
      where
      m.module = 16
      order by 
      c.shortname,
      s.section
    ) section on q.id = section.quiz_id
    where
  	g.timemodified >= :start_date
    and g.timemodified <= :end_date
    <QUIZ_CRITERIA>
    <AAA_CRITERIA>
    <ASHA_CRITERIA> 
    having 
    grade_percentage >= :pass_fail_percentage
    order by
    user.lastname,
    user.firstname,
    q.name";
  	
  	$base_params = array(
  	  'start_date' => $this->start_date,
  	  'end_date' => $this->end_date,
  	  'pass_fail_percentage' => (int) $this->pass_fail_percentage		
  	);
  	
  	switch ($type) {
  	  case 'asha':
  	  	$quiz_criteria = " and q.id = :quiz_id ";  
  	  	$aaa_criteria = "";
  	  	$asha_criteria = " and upper(asha) not like '%X%' ";
  	  	$query = str_replace("<QUIZ_CRITERIA>",$quiz_criteria,$base_query);
  	  	$query = str_replace("<AAA_CRITERIA>",$aaa_criteria,$query);
  	  	$query = str_replace("<ASHA_CRITERIA>",$asha_criteria,$query);
  	  	$params = $base_params;
  	  	$params['quiz_id'] = $this->quiz_id;
  	  	return (array('query' => $query, 'params' => $params));
  	  break;
  	  
  	  case 'aaa':
  	  	$quiz_criteria = " and q.id = :quiz_id ";
  	  	$aaa_criteria = " and upper(aaa) not like '%X%' ";
  	  	$asha_criteria = "";
  	  	$query = str_replace("<QUIZ_CRITERIA>",$quiz_criteria,$base_query);
  	  	$query = str_replace("<AAA_CRITERIA>",$aaa_criteria,$query);
  	  	$query = str_replace("<ASHA_CRITERIA>",$asha_criteria,$query);
  	  	$params = $base_params;
  	  	$params['quiz_id'] = $this->quiz_id;
  	  	return (array('query' => $query, 'params' => $params));
  	  break;
  	  	
  	  case 'general':
  	  	$quiz_criteria = "";
  	  	$aaa_criteria = "";
  	  	$asha_criteria = "";  	  	
  	  	$query = str_replace("<QUIZ_CRITERIA>",$quiz_criteria,$base_query);
  	  	$query = str_replace("<AAA_CRITERIA>",$aaa_criteria,$query);
  	  	$query = str_replace("<ASHA_CRITERIA>",$asha_criteria,$query);
  	  	$params = $base_params;
  	  	return (array('query' => $query, 'params' => $params));
  	  break;
  	  	
  	}
  }
  
  public function get_excel() {
  	$data = $this->get_report_data();
  	$end_date = new Datetime();
  	$end_date->setTimestamp($this->end_date);
  	
  	switch($this->report_name) {
  		
  	  // ASHA Report
  	  case 'asha_report':
  	  	// Creating a workbook
  	  	$workbook = new MoodleExcelWorkbook("-");
  	  	// Sending HTTP headers
  	  	$workbook->send('ASHA Report.xls');
  	  	// Adding the worksheet
  	  	$myxls = $workbook->add_worksheet('ASHA Report');
  	  	
  	  	$fields = array('Form_Type','Provider_code','CourseNumber','Part_forms','Offering_complete_date','number_attending','Partial_credit');
  	  	
  	  	// Row 0
  	  	foreach ($fields as $key => $f) {
  	  	  $myxls->write_string(0, $key, $f);
  	  	}
  	  	
  	  	// Row 1
  	  	$myxls->write_string(1, 0, 'A1');
  	  	$myxls->write_string(1, 1, 'ACES');
  	  	$myxls->write_string(1, 2, $this->asha_course_number);
  	  	$myxls->write_string(1, 3, count($data));
  	  	$myxls->write_string(1, 4, $end_date->format('mdY'));
  	  	$myxls->write_string(1, 5, count($data));
  	  	$myxls->write_string(1, 6, 'N');

  	  	// data starts on row 2
  	  	$r = 2;
  	  	foreach ($data as $d) {
  	  	  $myxls->write_string($r, 0, 'P1');
  	  	  $myxls->write_string($r, 1, $d->lastname . ' ' . $d->firstname);
  	  	  $myxls->write_string($r, 2, '');
  	  	  $myxls->write_string($r, 3, $d->asha);
  	  	  $r++;
  	  	}

  	  	$workbook->close();
  	  	exit();
  	  break;
  	
  	  // ASHA Reference Report
  	  case 'asha_reference_report':
  		// Creating a workbook
  		$workbook = new MoodleExcelWorkbook("-");
  		// Sending HTTP headers
  		$workbook->send('ASHA Reference Report.xls');
  		// Adding the worksheet
  		$myxls = $workbook->add_worksheet('ASHA Reference Report');
  		
  		$fields = array('Customer_Name','ASHA_ID','Address1','Address2','Address3','City','State','ZIP','Country','Primary_Phone','Email','Provider_RefNum');
  		
  		// Row 0
  		foreach ($fields as $key => $f) {
  			$myxls->write_string(0, $key, $f);
  		}
  		  		
  		// data starts on row 1
  		$r = 1;
  		foreach ($data as $d) {
  		  $myxls->write_string($r, 0, $d->lastname . ' ' . $d->firstname);
  		  $myxls->write_string($r, 1, $d->asha);
  		  $myxls->write_string($r, 2, $d->address1);  		  
  		  $myxls->write_string($r, 3, $d->address2);
  		  $myxls->write_string($r, 4, $d->address3);
  		  $myxls->write_string($r, 5, $d->city);
  		  $myxls->write_string($r, 6, $d->state);
  		  $myxls->write_string($r, 7, $d->zip);
  		  $myxls->write_string($r, 8, $d->country);
  		  $myxls->write_string($r, 9, $d->phone1);
  		  $myxls->write_string($r, 10, $d->email);
  		  $myxls->write_string($r, 11, $this->provider_refnum);
  		  $r++;
  		}
  		
  		$workbook->close();
  		exit();
  	  break;
  	  
  	  case 'aaa_report':
  	  	// get totals
  	  	$total_aaa = 0;
  	  	$total_aaa_passed = 0;
  	  	foreach ($data as $d) {
  	  	  if (!empty($d->aaa)) {
  	  	  	$total_aaa++;
  	  	  	if ($d->grade_percentage >= $this->pass_fail_percentage) {
  	  	  		$total_aaa_passed++;
  	  	  	}
  	  	  }
  	  	}
  	  	
  	  	// Creating a workbook
  	  	$workbook = new MoodleExcelWorkbook("-");
  	  	// Sending HTTP headers
  	  	$workbook->send('AAA Report.xls');
  	  	// Adding the worksheet
  	  	$myxls = $workbook->add_worksheet('AAA Report');
  	  	
  	  	// Row 0 - YYYY Program Report Form
  	  	$format = new MoodleExcelFormat();
  	  	$format->set_align('center');
  	  	$myxls->write_string(0, 0, $end_date->format('Y') . ' Program Report Form',$format);
  	  	$myxls->merge_cells(0, 0, 0, 8);

  	  	// Provider Name
  	  	$myxls->write_string(1, 0, 'Provider Name: ' . get_string('provider_name', 'report_capcsd'));
  	  	$myxls->merge_cells(1, 0, 1, 3);
  	  	
  	  	// Provider Contact
  	  	$myxls->write_string(1, 4, 'Provider Contact: ' . get_string('provider_contact', 'report_capcsd'));
  	  	$myxls->merge_cells(1, 4, 1, 8);
  	  	
  	  	// Course Code
  	  	$myxls->write_string(2, 0, 'Course Code: ' . $this->aaa_course_code);
  	  	$myxls->merge_cells(2, 0,2,4);
  	  	
  	  	// Course Title
  	  	$myxls->write_string(3, 0, 'Course Title: ' . $this->get_course_title($this->quiz_id));
  	  	$myxls->merge_cells(3, 0,3,8);
  	  	
  	  	// Course Date
  	  	$myxls->write_string(4, 0, 'Course Date: ' . $end_date->format('m/d/Y'));
  	  	$myxls->merge_cells(4, 0,4,3);
  	  	
  	  	// Course Location
  	  	$myxls->write_string(5, 0, 'Course Location: ' . get_string('course_location', 'report_capcsd'));
  	  	$myxls->merge_cells(5, 0,5,3);
  	  	
  	  	// # of CEUS Approved
  	  	$myxls->write_string(6, 0, '# of CEUs approved: ' . $this->ceus_approved);
  	  	$myxls->merge_cells(6, 0,6,3);
  	  	
  	  	
  	  	
  	  	// Total Number of Participants:  	  	
  	  	$myxls->write_string(7, 0, 'Total Number of Participants: ' . $total_aaa);
  	  	$myxls->merge_cells(7, 0,7,3);
  	  	
  	  	// Total Number of Participants requiring Academy CEUs:    
  	  	$myxls->write_string(8, 0, 'Total Number of Participants requiring Academy CEUs: ' . $total_aaa_passed);
  	  	$myxls->merge_cells(8, 0,8,5);
  	  	
  	  	// (only list participants applying for Academy CEUs)
  	  	$myxls->write_string(9, 0, '(only list participants applying for Academy CEUs)');
  	  	$myxls->merge_cells(9, 0,9,8);
  	  	
  	  	// **DO NOT DELETE ANY COLUMNS; LEAVE BLANK IF NOT APPLICABLE**
  	    $format = new MoodleExcelFormat();
  	  	$format->set_align('center');
  	  	$format->set_color('red');
  	  	$myxls->write_string(10, 0, '**DO NOT DELETE ANY COLUMNS; LEAVE BLANK IF NOT APPLICABLE**',$format);
  	  	$myxls->merge_cells(10, 0,10,8);
  	  	
  	  	
  	  	// Data Header
  	  	$myxls->write_string(11, 0, ''); // blank header for index
  	  	$myxls->write_string(11, 1, 'Course Code');
  	  	$myxls->write_string(11, 2, 'Academy Member ID');
  	  	$myxls->write_string(11, 3, 'Last Name');
  	  	$myxls->write_string(11, 4, 'First Name');
  	  	$myxls->write_string(11, 5, 'State');
  	  	$myxls->write_string(11, 6, 'Email');
  	  	$myxls->write_string(11, 7, 'CEUs');
  	  	$myxls->write_string(11, 8, 'Date');
  	  	
  	  	// Data
  	  	$r = 12;
  	  	$i = 1;
  	  	foreach ($data as $d) {
  	  	  $myxls->write_string($r, 0, $i); // blank header for index
  	  	  $myxls->write_string($r, 1, $this->aaa_course_code);
  	  	  $myxls->write_string($r, 2, $d->aaa);
  	  	  $myxls->write_string($r, 3, $d->lastname);
  	  	  $myxls->write_string($r, 4, $d->firstname);
  	  	  $myxls->write_string($r, 5, $d->state);
  	  	  $myxls->write_string($r, 6, $d->email);
  	  	  $myxls->write_string($r, 7, $this->ceus_approved);
  	  	  $myxls->write_string($r, 8, $end_date->format('m/d/Y'));
  	  	  $i++;
  	  	  $r++;
  	  	}
  	  	
  	  	$workbook->close();
  	  	exit();
  	  	
  	  break;
  	  
  	  // General  Report
  	  case 'general_report':
  	  	// Creating a workbook
  	  	$workbook = new MoodleExcelWorkbook("-");
  	  	// Sending HTTP headers
  	  	$workbook->send('General Report.xls');
  	  	// Adding the worksheet
  	  	$myxls = $workbook->add_worksheet('General Report');
  	  	
  	  	$fields = array('Customer_Name','ASHA_ID','AAA_ID','Address1','Address2','Address3','City','State','ZIP','Country','Primary_Phone','Email','Module','Grade','Date');
  	  	
  	  	// Row 0
  	  	foreach ($fields as $key => $f) {
  	  		$myxls->write_string(0, $key, $f);
  	  	}
  	  	
  	  	// data starts on row 1
  	  	$r = 1;
  	  	foreach ($data as $d) {
  	  		$myxls->write_string($r, 0, $d->lastname . ' ' . $d->firstname);
  	  		$myxls->write_string($r, 1, $d->asha);
  	  		$myxls->write_string($r, 2, $d->aaa);  	  		
  	  		$myxls->write_string($r, 3, $d->address1);
  	  		$myxls->write_string($r, 4, $d->address2);
  	  		$myxls->write_string($r, 5, $d->address3);
  	  		$myxls->write_string($r, 6, $d->city);
  	  		$myxls->write_string($r, 7, $d->state);
  	  		$myxls->write_string($r, 8, $d->zip);
  	  		$myxls->write_string($r, 9, $d->country);
  	  		$myxls->write_string($r, 10, $d->phone1);
  	  		$myxls->write_string($r, 11, $d->email);
  	  		$myxls->write_string($r, 12, $d->module);
  	  		$myxls->write_string($r, 13, $d->grade_percentage);
  	  		$myxls->write_string($r, 14, $d->time_completed);
  	  		$r++;
  	  	}
  	  	
  	  	$workbook->close();
  	  	exit();
  	  break;
  	  	
    }

  }
  
  public function get_course_title($quiz_id) {
  	global $DB;
  	
  	$q = "
  	select
  	s.name as course_title,
  	m.instance as quiz_id
  	from
  	{course} c
  	join {course_sections} s on c.id = s.course
  	join {course_modules} m on c.id = m.course and s.id = m.section
  	where
  	m.module = 16
  	and m.instance = :quiz_id";
  	
  	$result = $DB->get_record_sql($q,array('quiz_id' => $quiz_id));
  	
  	return $result->course_title;
  	
  }
  
}
	