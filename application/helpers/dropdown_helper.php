<?php
/**
 * This file contains functions that are used in loading data for drop downs
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 01/08/2015
 */




# Get a list of options
# Allowed return values: [div, option]
function get_option_list($obj, $list_type, $return = 'div', $searchBy="", $more=array())
{
	$optionString = '';

	switch($list_type)
	{
		case "district":
			$districts = $obj->_query_reader->get_list('get_list_of_districts');
			foreach($districts AS $row)
			{
				$optionString .= "<div data-value='".$row['value']."'>".$row['display']."</div>";
			}
		break;

		case "institutions":
			$searchString = !empty($searchBy)? htmlentities(restore_bad_chars($searchBy), ENT_QUOTES): "";
			$searchQuery = !empty($searchString)? " (MATCH(name) AGAINST('+".implode(" +",explode(" ",$searchString))."') OR name LIKE '".$searchString."%' OR name LIKE '% ".$searchString."%') ": " 1=1 ";
			$institutions = $obj->_query_reader->get_list('get_list_of_institutions', array('search_query'=>$searchQuery));
			foreach($institutions AS $row)
			{
				$optionString .= "<div data-value='".$row['value']."'>".$row['display']."</div>";
			}
		break;


		case "country":
			$countries = $obj->_query_reader->get_list('get_list_of_countries');
			foreach($countries AS $row)
			{
				$optionString .= "<div data-value='".$row['value']."'>".$row['display']."</div>";
			}
		break;


		case "county":
			$searchString = !empty($searchBy)? htmlentities(restore_bad_chars($searchBy), ENT_QUOTES): "";
			$searchQuery = !empty($searchString)? " (C.name LIKE '".$searchString."%' OR C.name LIKE '% ".$searchString."%') ": " 1=1 ";
			$orderBy = " C.name ASC";

			# Get the district field if given
			$district = array_key_contains('-district-', $more);

			$counties = $obj->_query_reader->get_list('get_county_list_data', array('search_query'=>$searchQuery, 'order_by'=>$orderBy, 'limit_text'=>'100'));
			foreach($counties AS $row)
			{
				$optionString .= "<div data-value='".$row['value']."'";
				$optionString .= $district['boolean']? " onclick=\"universalUpdate('".$more[$district['key']]."', '".$row['district_name']."')\"": "";
				$optionString .= " >".$row['display']."</div>";
			}
		break;


		case "citizentype":
			$types = array('By Birth', 'By Naturalization', 'By Registration');
			foreach($types AS $row)
			{
				$optionString .= "<div data-value='".$row."'>".$row."</div>";
			}
		break;


		case "institutiontype":
			$types = array('University', 'College', 'Technical', 'Secondary', 'Primary');
			foreach($types AS $row)
			{
				$optionString .= "<div data-value='".$row."'>".$row."</div>";
			}
		break;


		case "month":
			$months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
			foreach($months AS $row)
			{
				$optionString .= "<div data-value='".$row."'>".$row."</div>";
			}
		break;


		case "pastyear":
			for($i=date('Y'); $i>(date('Y') - 80); $i--)
			{
				$optionString .= "<div data-value='".$i."'>".$i."</div>";
			}
		break;


		case "subjecttype":
			$types = array('Major', 'Other', 'Minor');
			foreach($types AS $row)
			{
				$optionString .= "<div data-value='".$row."'>".$row."</div>";
			}
		break;


		case "jobroles":
			$roles = $obj->_query_reader->get_list('get_duties');
			foreach($roles AS $row)
			{
				$optionString .= "<div data-value='".$row['value']."'>".$row['display']."</div>";
			}
		break;


		case "roles":
			$roles = $obj->_query_reader->get_list('get_permission_groups', array('system_only'=>"'N','Y'"));
			foreach($roles AS $row)
			{
				$optionString .= "<div data-value='".$row['value']."'>".$row['display']."</div>";
			}
		break;


		case "schooljobs":
			$searchString = !empty($searchBy)? htmlentities(restore_bad_chars($searchBy), ENT_QUOTES): "";
			$searchQuery = !empty($searchString)? " (V.topic LIKE '".$searchString."%' OR V.topic LIKE '% ".$searchString."%') ": " 1=1 ";

			$jobs = $obj->_query_reader->get_list('get_school_jobs', array('user_id'=>$obj->native_session->get('__user_id'), 'search_query'=>$searchQuery));
			foreach($jobs AS $row)
			{
				$optionString .= "<div data-value='".$row['value']."'onclick=\"universalUpdate('vacancyid', '".$row['id']."')\">".$row['display']."</div>";
			}

		break;


		case "users":
			$searchString = !empty($searchBy)? htmlentities(restore_bad_chars($searchBy), ENT_QUOTES): "";
			$searchQuery = !empty($searchString)? " (P.first_name LIKE '".$searchString."%' OR P.first_name LIKE '% ".$searchString."%' OR P.last_name LIKE '".$searchString."%') AND U.status='active' ": " U.status='active' ";
			$orderBy = " ORDER BY P.last_name ASC";

			$users = $obj->_query_reader->get_list('get_user_list_data', array('search_query'=>" U.id <> '".$obj->native_session->get('__user_id')."' AND ".$searchQuery, 'order_by'=>$orderBy, 'limit_text'=>'100'));

			foreach($users AS $row)
			{
				$optionString .= "<div data-value='".$row['value']."' onclick=\"universalUpdate('userid', '".$row['id']."')\">".$row['display']."</div>";
			}
		break;


		case "schooltypes":
			$types = array('Primary', 'Secondary', 'Tertiary', 'University', 'Other');
			foreach($types AS $row)
			{
				$optionString .= "<div data-value='".$row."'>".$row."</div>";
			}
		break;


		case "teachers":
			$searchString = !empty($searchBy)? htmlentities(restore_bad_chars($searchBy), ENT_QUOTES): "";
			$searchQuery = !empty($searchString)? " (P.first_name LIKE '".$searchString."%' OR P.first_name LIKE '% ".$searchString."%' OR P.last_name LIKE '".$searchString."%') AND U.status='active' ": " U.status='active' ";
			$orderBy = " ORDER BY P.last_name ASC";

			if($obj->native_session->get('__permission_group') && $obj->native_session->get('__permission_group') == '3')
			{
				if($obj->native_session->get('__posting'))
				{
					$users = $obj->_query_reader->get_list('get_user_list_data', array('search_query'=>$searchQuery." AND PS.institution_id='".$obj->native_session->get('__posting')."'  AND U.permission_group_id='2' ", 'order_by'=>$orderBy, 'limit_text'=>'100'));
				}
				else $users = array();
			}
			else
			{
				$users = $obj->_query_reader->get_list('get_user_list_data', array('search_query'=>$searchQuery." AND U.permission_group_id='2' ", 'order_by'=>$orderBy, 'limit_text'=>'100'));
			}

			foreach($users AS $row)
			{
				$optionString .= "<div data-value='".$row['value']."' onclick=\"universalUpdate('teacherid', '".$row['id']."')\">".$row['display']."</div>";
			}
		break;



		case "contactreason":
			$reasons = array('Account Access', 'Job Application', 'Information Security', 'General Inquiries');
			foreach($reasons AS $row)
			{
				$optionString .= "<div data-value='".$row."'>".$row."</div>";
			}
		break;


		case "grades":
		    $searchstring = '';
			if(!empty($more['category']))
			{
				$searchString = ' AND grade_category = "'.$more['category'].'" ';
				//SEARCHSTRING
			}
			$grades = $obj->_query_reader->get_list('get_teacher_grades',array('searchstring'=>$searchString));
			foreach($grades AS $row)
			{
				$optionString .= "<div data-value='".$row['value']."'>".$row['display']."</div>";
			}
		break;
		
		
		case "categories":
		#exit("reached");	
			$grades = $obj->_query_reader->get_list('grade_category');
			foreach($grades AS $row)
			{
				$optionString .= "<div data-value='".$row['value']."' class='grade_category'  >".$row['display']."</div>";
			}
		break;



		case "interviewresults":
			$results = array('Failed','Inconclusive','Passed','Awarded');
			foreach($results AS $row)
			{
				$optionString .= "<div data-value='".$row."'>".$row."</div>";
			}
		break;


		case "shortlists":
			$searchString = !empty($searchBy)? htmlentities(restore_bad_chars($searchBy), ENT_QUOTES): "";
			$searchQuery = !empty($searchString)? " vacancy_id='".$more['jobid']."' AND shortlist_name LIKE '".$searchString."%' ": " vacancy_id='".$more['jobid']."' ";
			$orderBy = " ORDER BY shortlist_name ASC";

			$shortlists = $obj->_query_reader->get_list('get_shortlist_data', array('search_query'=>$searchQuery, 'order_by'=>$orderBy, 'limit_text'=>'100'));
			foreach($shortlists AS $row)
			{
				$optionString .= "<div data-value='".$row['value']."'>".$row['display']."</div>";
			}
		break;


		case "schools":
			$searchString = !empty($searchBy)? htmlentities(restore_bad_chars($searchBy), ENT_QUOTES): "";
			$searchQuery = !empty($searchString)? " (INS.name LIKE '".$searchString."%' OR INS.name LIKE '% ".$searchString."%') ": " 1=1 ";
			$orderBy = " ORDER BY I.name ASC";

			$schools = $obj->_query_reader->get_list('get_institution_data', array('search_query'=>$searchQuery, 'order_by'=>$orderBy, 'limit_text'=>'100'));
			foreach($schools AS $row)
			{
				$optionString .= "<div data-value='".$row['value']."' onclick=\"universalUpdate('schoolid', '".$row['id']."')\">".$row['display']."</div>";
			}
		break;



		case "leavetypes":
			$reasons = array('Study Leave', 'Normal Leave', 'Marternity Leave', 'Other Leave');
			foreach($reasons AS $row)
			{
				$optionString .= "<div data-value='".$row."'>".$row."</div>";
			}
		break;



		case "documenttypes":
			$reasons[0] = array('type'=>'confirmation_letter', 'display'=>'Job Confirmation Letter');
			$reasons[1] = array('type'=>'transfer_letter', 'display'=>'Job Transfer Letter');
			$reasons[2] = array('type'=>'transfer_pca', 'display'=>'Job Transfer PCA');
			$reasons[3] = array('type'=>'verification_letter', 'display'=>'Leave Confirmation Letter');
			$reasons[4] = array('type'=>'retirement_letter', 'display'=>'Retirement Confirmation Letter');
			$reasons[5] = array('type'=>'registration_certificate', 'display'=>'Teacher Registration Certificate');

			foreach($reasons AS $row)
			{
				$optionString .= "<div data-value='".$row['display']."' onclick=\"universalUpdate('documenttype', '".$row['type']."')\">".$row['display']."</div>";
			}
		break;



		case "boards":
		case "viewonlyboards":
			$boards = $obj->_query_reader->get_list('get_list_of_boards');
			foreach($boards AS $row)
			{
				$optionString .= "<div data-value='".$row['value']."' onclick=\"universalUpdate('boardid', '".$row['id']."')";
				if($list_type == "boards"){
					$optionString .= ";updateFieldLayer('".base_url()."interview/view_board_members/boardid/".$row['id']."','','','memberlist_div','');";
				}

				$optionString .= "\">".$row['display']."</div>";
			}
		break;



		case "reporttypes":
			$types[0] = array('type'=>'registerednumbers', 'display'=>'Number of Registered Teachers');
			$types[1] = array('type'=>'teacherappointments', 'display'=>'Teacher Appointments');
			$types[2] = array('type'=>'teacherstatus', 'display'=>'Teacher Status');

			foreach($types AS $row)
			{
				$optionString .= "<div data-value='".$row['display']."' onclick=\"updateFieldLayer('".base_url()."teacher/report_specification/type/".$row['type']."','','','reportspecs','')\">".$row['display']."</div>";
			}
		break;



		case "registerednumbers":
			$types[0] = array('type'=>'gender', 'display'=>'By Gender');
			$types[1] = array('type'=>'grades', 'display'=>'By Grade');
			$types[2] = array('type'=>'subjects', 'display'=>'By Subject');
			$types[3] = array('type'=>'schools', 'display'=>'By School');
			$types[4] = array('type'=>'districts', 'display'=>'By District');
			$types[5] = array('type'=>'region', 'display'=>'By Region');

			foreach($types AS $row)
			{
				$optionString .= "<div data-value='".$row['display']."'>".$row['display']."</div>";
			}
		break;



		case "teacherappointments":
			$types[0] = array('type'=>'post', 'display'=>'By Post');
			$types[1] = array('type'=>'school', 'display'=>'By School');
			$types[2] = array('type'=>'district', 'display'=>'By District');
			$types[3] = array('type'=>'region', 'display'=>'By Region');

			foreach($types AS $row)
			{
				$optionString .= "<div data-value='".$row['display']."'>".$row['display']."</div>";
			}
		break;



		case "teacherstatus":
			$types[0] = array('type'=>'applied', 'display'=>'Applied');
			$types[1] = array('type'=>'probation', 'display'=>'On Probation');
			$types[2] = array('type'=>'confirmed', 'display'=>'Confirmed');
			$types[3] = array('type'=>'onleave', 'display'=>'On Leave');
			$types[4] = array('type'=>'retired', 'display'=>'Retired');

			foreach($types AS $row)
			{
				$optionString .= "<div data-value='".$row['display']."'>".$row['display']."</div>";
			}
		break;


        #fetch subject specializations from teaching_subject table.
		case "subjectspecialization":
		$searchString = !empty($searchBy)? htmlentities(restore_bad_chars($searchBy), ENT_QUOTES): "";
		$searchQuery = !empty($searchString)? " AND subject LIKE '".$searchString."%' ": "";
		$orderBy = " ORDER BY subject ASC";
		$subjects = $obj->_query_reader->get_list('get_list_subjects', array('search_query'=>$searchQuery, 'order_by'=>$orderBy, 'limit_text'=>'100'));

		foreach($subjects AS $row)
		{
			$optionString .= "<div data-value='".$row['id']."' onclick=\"universalUpdate('subject', '".$row['id']."')\">".$row['subject']."</div>";
		}
		break;


		#fetch courses offered
		case 'courses':
		
		//$optionString .="<div>Courses</div>";
		$searchString = !empty($searchBy)? htmlentities(restore_bad_chars($searchBy), ENT_QUOTES): "";
		$searchQuery = !empty($searchString)? " AND name LIKE '".$searchString."%' ": "";
		$orderBy = " ORDER BY name ASC";
		$subjects = $obj->_query_reader->get_list('fetch_courses', array('search_query'=>$searchQuery, 'order_by'=>$orderBy, 'limit_text'=>'100'));

		foreach($subjects AS $row)
		{
			//$optionString .= "<div data-value='".$row['id']."' onclick=\"universalUpdate('course', '".$row['id']."')\">".$row['name']."</div>";
			$optionString .= "<div  data-ref=\"hidden_courses\" class=\"listtag\" data-value='".$row['id']."' onclick=\"listtags('".$row['name']."', '".$row['id']."');\">".$row['name']."</div>";
		}
		
		break;


		#fetch particulars
		case 'particulars':
		$searchString = !empty($searchBy)? htmlentities(restore_bad_chars($searchBy), ENT_QUOTES): "";
		$searchQuery = !empty($searchString)? " AND tags.parent_type like 'institution' AND tags.name LIKE '".$searchString."%' ": " AND tags.parent_type like 'institution' ";
		$orderBy = " ORDER BY tags.name ASC";
		$subjects = $obj->_query_reader->get_list('get_list_tags', array('search_query'=>$searchQuery, 'order_by'=>$orderBy, 'limit_text'=>'100'));

		foreach($subjects AS $row)
		{
			$optionString .= "<div  data-ref=\"hidden_particulars\" class=\"listtag\" data-value='".$row['id']."' onclick=\"listtags('".$row['name']."', '".$row['id']."');\">".$row['name']."</div>";
		}
		break;


		//fetch highest class list
		case 'highestclass':
		$optionString .="<div>Highest Class </div>";
		break;





	}

	return !empty($optionString)? $optionString: (($return == 'div')? "<div data-value=''>No options available</div>": "<option value=''>No options available</option>");
}






?>
