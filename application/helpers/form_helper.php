<?php
/**
 * This helper manages form data on the system.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 01/13/2015
 */


# Process data from the form
function process_fields($obj, $data, $required=array(), $allowChars=array())
{ 
	$disallowChars = array("'", "\"", "\\", "(", ")", "/", "<", ">", "!", "#", "%", "&", "?", "$", ":", ";", "=", "*");
	if(!empty($allowChars)) $disallowChars = array_diff($disallowChars, $allowChars);
	# Did the data set pass the requried check
	$hasPassed = true;
	$finalData = array();
	$msg = "";

	foreach($data AS $key=>$value)
	{
		# Do not validate arrays
		if(!is_array($value))
		{
			$value = htmlentities(trim($value), ENT_QUOTES);
			# Add if the string is not empty and does not contain any of the disallowed characters
			if(!empty($value) && !(0 < count(array_intersect(str_split(strtolower($value)), $disallowChars))) )
			{

				# If this is a birthday, check to see whether they are above 18 years of age
				if($key == 'birthday'){
					$userDate = new DateTime($value);
					$today = new DateTime('now');
					$difference = $userDate->diff($today);

					if(!($difference->y >= 18)){
						$msg = "WARNING: The submitted birth date is not valid for a teacher.";
						$hasPassed = false;
					}
				}
				if($key =='averageworkload')
				{
					if($value > 45)
					{
						$msg = "WARNING: Average weekly workload exceeds the set maximum ";
						$hasPassed = false;
					}
				}
				$obj->native_session->set($key, $value);
				$finalData[$key] = $value;
			}
			# Catch the case where a required field was not entered
			else if(in_array($key, $required))
			{
				$hasPassed = false;
				$msg = "WARNING: Invalid Characters Entered.";
				$finalData[$key] = $value;
			}
		}
	}

	return array('boolean'=>$hasPassed, 'data'=>$finalData, 'msg'=>$msg);
}




# Copy the address provided in one field to another field
function copy_address($obj, $data)
{
	$from = $data['from'];
	$to = $data['to'];

	if($obj->native_session->get($from.'__addresstype')) $obj->native_session->set($to.'__addresstype', $obj->native_session->get($from.'__addresstype'));
	if($obj->native_session->get($from.'__addressline')) $obj->native_session->set($to.'__addressline', $obj->native_session->get($from.'__addressline'));
	if($obj->native_session->get($from.'__county')) $obj->native_session->set($to.'__county', $obj->native_session->get($from.'__county'));
	if($obj->native_session->get($from.'__district')) $obj->native_session->set($to.'__district', $obj->native_session->get($from.'__district'));
	if($obj->native_session->get($from.'__country')) $obj->native_session->set($to.'__country', $obj->native_session->get($from.'__country'));

	# Simply return a confirmation that the address country is set
	return $obj->native_session->get($from.'__country')? true: false;
}





# Remove address data from a field
function remove_address($obj, $data)
{
	$id = $data['field_id'];

	$obj->native_session->delete($id.'__addresstype');
	$obj->native_session->delete($id.'__addressline');
	$obj->native_session->delete($id.'__county');
	$obj->native_session->delete($id.'__district');
	$obj->native_session->delete($id.'__country');

	# Simply return a confirmation that the address country does not exist any more
	return $obj->native_session->get($id.'__country')? false: true;
}
?>
