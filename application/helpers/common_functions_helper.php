<?php
/**
 * This file contains common functions used in the system.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 01/08/2015
 */






#Function to filter forwarded data to get only the passed variables
#In addition, it picks out all non-zero data from a URl array to be passed to a form
function filter_forwarded_data($obj, $urlDataArray=array(), $reroutedUrlDataArray=array(), $noOfPartsToIgnore=RETRIEVE_URL_DATA_IGNORE)
{
	# Get the passed details into the url data array if any
	$urlData = $obj->uri->uri_to_assoc($noOfPartsToIgnore, $urlDataArray);
	
	$dataArray = array();
	
	
	foreach($urlData AS $key=>$value)
	{
		if($value !== FALSE && trim($value) != '' && !array_key_exists($value, $urlData))
		{
			if($value == '_'){
				$dataArray[$key] = '';
			} else {
				$dataArray[$key] = $value;
			}
		}
	}
	
	#handle re-routed URL data
	if(!empty($reroutedUrlDataArray))
	{
		$urlInfo = $obj->uri->ruri_to_assoc(3);
		foreach($reroutedUrlDataArray AS $urlKey)
		{
			if(!empty($urlInfo[$urlKey]))
			{
				$dataArray[$urlKey] = $urlInfo[$urlKey];
			}
		}
	}
	
	return restore_bad_chars_in_array($dataArray);
}







# Goes through a row returned from a form escaping quotes and neutralising HTML insertions
function clean_form_data($formData)
{
	$cleanData = array();
	
	foreach($formData AS $key=>$value)
	{
		if(is_array($value))
		{
			foreach($value AS $subKey=>$subValue)
			{
				if(is_array($subValue))
				{
					foreach($subValue AS $subSubKey=>$subSubValue)
					{
						if(is_array($subSubValue))
						{
							foreach($subSubValue AS $subSubSubKey=>$subSubSubValue)
							{
								$cleanData[$key][$subKey][$subSubKey][$subSubSubKey] = htmlentities(trim($subSubSubValue), ENT_QUOTES);
							}
						}
						else
						{
							$cleanData[$key][$subKey][$subSubKey] = htmlentities(trim($subSubValue), ENT_QUOTES);
						}
					}
				}
				else
				{
					$cleanData[$key][$subKey] = htmlentities(trim($subValue), ENT_QUOTES);
				}
			}
		}
		else
		{
			$cleanData[$key] = htmlentities(trim($value), ENT_QUOTES); 
		}
	}
	
	return $cleanData;
}





#Checks if a password is valid
function is_valid_password($password, $validationSettings=array())
{
	$isValid = true;
	$minLength = !empty($validationSettings['minLength'])? $validationSettings['minLength']: 8;
	$maxLength = !empty($validationSettings['maxLength'])? $validationSettings['maxLength']: 60;
	$needsChar = !empty($validationSettings['needsChar'])? $validationSettings['needsChar']: false;
	$needsNumber = !empty($validationSettings['needsNumber'])? $validationSettings['needsNumber']: false;
	
	if(empty($password))
	{
		$isValid = false;
	}
	else if(strlen($password) < $minLength)
	{
		$isValid = false;
	}
	else if(strlen($password) > $maxLength)
	{
		$isValid = false;
	}
	#TODO: Fix preg_match regexpression
	else if($needsChar && !preg_match('/[[:punct:]]/', $password))
	{
		$isValid = false;
	}
	#TODO: Fix preg_match regexpression
	else if($needsNumber && !preg_match('/^[0-9]+$/', $password))
	{
		$isValid = false;
	}
	
	return $isValid;
}







# Returns the passed message with the appropriate formating based on whether it is an error or not
function format_notice($obj, $msg)
{
	$style = "border-radius: 5px;
	-moz-border-radius: 5px;";
	
	if(is_array($msg))
	{
		$result = $obj->_query_reader->run('save_error_msg', array('msgcode'=>$msg['code'], 'details'=>$msg['details'], 'username'=>$obj->session->userdata('username'), 'ipaddress'=>$obj->input->ip_address() ));
	
		$msg = $msg['details'];
	}
    
	# Error message. look for "WARNING:" in the message
	if(strcasecmp(substr($msg, 0, 8), 'WARNING:') == 0)
	{
		$msgString = "<table width='100%' border='0' cellspacing='0' cellpadding='5' style=\"".$style."border:0px;\">".
						"<tr><td width='1%' class='error' style='border:0px;padding:5px;min-width:0px;' nowrap>".str_replace("WARNING:", "<img src='".base_url()."assets/images/warning.png' border='0'/></td><td  class='error'  style='font-size:13px; color:#000;border:0px;' width='99%' valign='middle'>", $msg)."</td></tr>".
					  "</table>";
	}
	# Error message. look for "ERROR:" in the message
	else if(strcasecmp(substr($msg, 0, 6), 'ERROR:') == 0)
	{
		$msgString = "<table width='100%' border='0' cellspacing='0' cellpadding='5' style=\"".$style."border:0px;\">".
						"<tr><td class='error' style='border:0px;padding:5px;min-width:0px;' width='1%' nowrap>".str_replace("ERROR:", "<img src='".base_url()."assets/images/error.png'  border='0'/></td><td  width='99%' class='error'  style='font-size:13px;border:0px;' valign='middle'>", $msg)."</td></tr>".
					  "</table>";
		
		$userId = $obj->native_session->get('__user_id')? $obj->native_session->get('__user_id'): 'UNKNOWN';
		$email = $obj->native_session->get('__email_address')? $obj->native_session->get('__email_address'): 'UNKNOWN';
		$obj->_logger->add_event(array('log_code'=>'system_error', 'result'=>'fail', 'details'=>"userid=".$userId."|email=".$email."|msg=".$msg));
	}
	
	#Normal Message
	else
	{
		$msgString = "<table width='100%' border='0' cellspacing='0' cellpadding='5' style=\"".$style."border:0px;\">".
						"<tr><td class='message' style='border:0px;' nowrap>".$msg."</td></tr>".
					  "</table>";
	}
	
	return $msgString;
}





#Function to fomart a notice string to the appropriate color
function format_status($status)
{
	$statusString = str_replace('_', ' ', $status);
	
	if(strtolower($status) == 'pending' || strtolower($status) == 'suspended' || strtolower($status) == 'inactive' || strtolower($status) == 'unopened')
	{
		$statusString = "<span class='orange'>".$status."</span>";
	}
	elseif(strtolower($status) == 'joined' || strtolower($status) == 'active' || strtolower($status) == 'already_member' || strtolower($status) == 'member')
	{
		$statusString = "<span class='green'>".$status."</span>";
	}
	elseif(strtolower($status) == 'bounced' || strtolower($status) == 'blocked' || strtolower($status) == 'deleted' || strtolower($status) == 'not_eligible')
	{
		$statusString = "<span class='red'>".$status."</span>";
	}
	elseif(strtolower($status) == 'read' || strtolower($status) == 'clicked')
	{
		$statusString = "<span class='blue'>".$status."</span>";
	}
	
	return $statusString;
}





# Function that encrypts the entered values
function encrypt_value($value)
{
	$num = strlen($value);
	$numIndex = $num-1;
	$newValue="";
		
	#Reverse the order of characters
	for($x=0;$x<strlen($value);$x++){
		$newValue .= substr($value,$numIndex,1);
		$numIndex--;
	}
		
	#Encode the reversed value
	$newValue = base64_encode($newValue);
	return $newValue;
}
	
	
#Function that decrypts the entered values
function decrypt_value($dvalue)
{
	#Decode value
	$dvalue = base64_decode($dvalue);
		
	$dnum = strlen($dvalue);
	$dnumIndex = $dnum-1;
	$newDvalue = "";
		
	#Reverse the order of characters
	for($x=0;$x<strlen($dvalue);$x++){
		$newDvalue .= substr($dvalue,$dnumIndex,1);
		$dnumIndex--;
	}
	return $newDvalue;
}



# Function to replace placeholders for bad characters in a text passed in URL with their actual characters
function restore_bad_chars($goodString)
{
	$badString = '';
	$badChars = array("'", "\"", "\\", "(", ")", "/", "<", ">", "!", "#", "@", "%", "&", "?", "$", ",", " ", ":", ";", "=", "*");
	$replaceChars = array("_QUOTE_", "_DOUBLEQUOTE_", "_BACKSLASH_", "_OPENPARENTHESIS_", "_CLOSEPARENTHESIS_", "_FORWARDSLASH_", "_OPENCODE_", "_CLOSECODE_", "_EXCLAMATION_", "_HASH_", "_EACH_", "_PERCENT_", "_AND_", "_QUESTION_", "_DOLLAR_", "_COMMA_", "_SPACE_", "_FULLCOLON_", "_SEMICOLON_", "_EQUAL_","_ASTERISK_");
	
	foreach($replaceChars AS $pos => $charEquivalent)
	{
		$badString = str_replace($charEquivalent, $badChars[$pos], $goodString);
		$goodString = $badString;
	}
	
	return $badString;
}

# Function to replace bad characters before they are passed in a URL
function replace_bad_chars($badString)
{
	$badChars = array("'", "\"", "\\", "(", ")", "/", "<", ">", "!", "#", "@", "%", "&", "?", "$", ",", " ", ":", ";", "=");
	$replaceChars = array("_QUOTE_", "_DOUBLEQUOTE_", "_BACKSLASH_", "_OPENPARENTHESIS_", "_CLOSEPARENTHESIS_", "_FORWARDSLASH_", "_OPENCODE_", "_CLOSECODE_", "_EXCLAMATION_", "_HASH_", "_EACH_", "_PERCENT_", "_AND_", "_QUESTION_", "_DOLLAR_", "_COMMA_", "_SPACE_", "_FULLCOLON_", "_SEMICOLON_", "_EQUAL_");
	$goodString = '';
	
	foreach($badChars AS $pos => $char){
		$goodString = str_replace($char, $replaceChars[$pos], $badString);
		$badString = $goodString;
	}
	
	return $goodString;
}


# Restore bar chars in an array
function restore_bad_chars_in_array($goodArray)
{
	$badArray = array();
	
	foreach($goodArray AS $key=>$item)
	{
		$badArray[$key] = restore_bad_chars($item);
	}
	
	return $badArray;
}







# Returns the AJAX constructor to a page where needed
function get_ajax_constructor($needsAjax, $extraIds=array())
{
	$ajaxString = "";
	
	if($needsAjax)
	{
		$ajaxString = "<script language=\"javascript\"  type=\"text/javascript\">".
							"var http = getHTTPObject();";
							
		if(!empty($extraIds))
		{
			foreach($extraIds AS $id)
			{
				$ajaxString .=  "var ".$id." = getHTTPObject();";
			}
		}					
		$ajaxString .=  "</script>";
	}
	return $ajaxString;
}




//Function to return a number with two decimal places and a comma after three places
function add_commas($number, $noDecimalPlaces=2)
{
	if(!isset($number) || $number == "" ||  $number <= 0)
	{
		return number_format('0.00', $noDecimalPlaces, '.', ',');
	} 
	else 
	{
		return number_format(remove_commas($number), $noDecimalPlaces, '.', ',');
	}
}
	
//Function to remove commas before saving to the database
function remove_commas($number)
{
	return clean_str(str_replace(",","",$number));
}

	
//Function to remove quotes before saving to the database
function remove_quotes($string)
{
	return str_replace('"', '', str_replace("'", '', $string));
}
	
//Function to clean user input so that it doesnt break the display functions
//This also helps disable hacker bugs
function clean_str($strInput)
{
	return htmlentities(trim($strInput));
}


	
	


#Function to get current user's IP address
function get_ip_address()
{
	$ip = "";
	if ( isset($_SERVER["REMOTE_ADDR"]) )    
	{
    	$ip = ''.$_SERVER["REMOTE_ADDR"];
	} 
	else if ( isset($_SERVER["HTTP_X_FORWARDED_FOR"]) )    
	{
    	$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	} 
	else if ( isset($_SERVER["HTTP_CLIENT_IP"]) )
	{
    	$ip = $_SERVER["HTTP_CLIENT_IP"];
	}
	
	return (ENVIRONMENT == 'development' || (!empty($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== FALSE))? DEFAULT_IP: $ip;
}



function get_current_uri($escapeQuotes=TRUE)
{
	$link =  "//".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	
	return $escapeQuotes? htmlspecialchars($link, ENT_QUOTES, 'UTF-8'): $link;
}





#Function to format phone number for display
function format_phone_number($number, $country='USA')
{
	$finalNumber = "";
	if(!empty($number))
	{
		#For 10 digit countries
		if(in_array($country, array('USA')))
		{
			#+1(213)123-4567
			$finalNumber = preg_replace('~.*(\d{3})[^\d]*(\d{3})[^\d]*(\d{4}).*~', '($1) $2-$3', $number);
		}
	}
	
	return $finalNumber;
}




# This function converts a binary string to hexadecimal characters.
# @param $bytes  Input string.
# @return String with lowercase hexadecimal characters.
function string_to_hex($bytes) 
{
	$ret = '';
	for($i = 0; $i < strlen($bytes); $i++) {
		$ret .= sprintf('%02x', ord($bytes[$i]));
	}
	return $ret;
}


#Function to generate random bytes
function generate_random_bytes($length) 
{
	# Use mt_rand to generate $length random bytes. 
	$data = '';
	for($i = 0; $i < $length; $i++) 
	{
		$data .= (rand()%9);
	}

	return $data;
}
	
#Function to generate an ID
function generate_id() 
{
	return '_' . string_to_hex(generate_random_bytes(21));
}


	
# Function checks all values to see if they are all true and returns the value TRUE or FALSE
function get_decision($values_array, $defaultTo=TRUE)
{
	$decision = empty($values_array)? $defaultTo: TRUE;
	
	if(empty($values_array))
	{
		foreach($values_array AS $value)
		{
			if(!$value)
			{
				$decision = FALSE;
				break;
			}
		}
	}
	
	return $decision;
}


#Function to hide digits of a string given and show only the part desired
function hide_digits($fullString, $showLast=2, $hideChar='*')
{
	$fullLength = strlen($fullString);
	$hideLength = $fullLength - $showLast;
	$finalString = "";
	for($i=0;$i<$hideLength; $i++)
	{
		$finalString .= $hideChar;
	}
	
	#Add the part not to be hidden
	$finalString .= substr($fullString, -$showLast);
	
	return $finalString;
}




#Validate an email address. If the email address is not required, then an empty string will be an acceptable
#value for the email address
function is_valid_email($email, $isRequired = true)
{
	   $isValid = true;
	   $atIndex = strrpos($email, "@");
	   
	   #if email is not required and is an empty string, do not check it. Return True.
	   if(!$isRequired && empty($email)){
		   return true;
	   }
	   if (is_bool($atIndex) && !$atIndex){
		  $isValid = false;
	   } else {
		  $domain = substr($email, $atIndex+1);
		  $local = substr($email, 0, $atIndex);
		  $localLen = strlen($local);
		  $domainLen = strlen($domain);
		  
		if ($localLen < 1 || $localLen > 64) {
			 # local part length exceeded
			 $isValid = false;
		  } else if ($domainLen < 1 || $domainLen > 255) {
			 # domain part length exceeded
			 $isValid = false;
		  }  else if ($local[0] == '.' || $local[$localLen-1] == '.') {
			 # local part starts or ends with '.'
			 $isValid = false;
		  } else if (preg_match('/\\.\\./', $local)) {
			 # local part has two consecutive dots
			 $isValid = false;
		  } else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
			 # character not valid in domain part
			 $isValid = false;
		  } else if (preg_match('/\\.\\./', $domain)) {
			 # domain part has two consecutive dots
			 $isValid = false;
		  } else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local))) {
			 # character not valid in local part unless 
			 # local part is quoted
			 if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local))) {
				$isValid = false;
			 }
		  } else if (strpos($domain, '.') === FALSE) {
			 # domain has no period
			 $isValid = false;
		  }
		  
		 /* if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))) {
			 # domain not found in DNS
			 $isValid = false;
		  } */
	 }
	 #return true if all above pass
	 return $isValid;
}
	
	
	
#Validate a delimited list of email addresses
function is_valid_email_list($emaillist, $isRequired = true, $delimiter = ",") 
{
	$list = explode($delimiter, $emaillist); 
	foreach ($list as $email) {
		if (!is_valid_email($email, $isRequired)) {
			return false; 
		} 
	}
	return true; 
}




#Convert from one base to another
function convert_bases($numberInput, $fromBaseInput, $toBaseInput)
{
    if ($fromBaseInput==$toBaseInput) return $numberInput;
    $fromBase = str_split($fromBaseInput,1);
    $toBase = str_split($toBaseInput,1);
    $number = str_split($numberInput,1);
    $fromLen=strlen($fromBaseInput);
    $toLen=strlen($toBaseInput);
    $numberLen=strlen($numberInput);
    $retval='';
    if ($toBaseInput == '0123456789')
    {
        $retval=0;
        for ($i = 1;$i <= $numberLen; $i++)
            $retval = bcadd($retval, bcmul(array_search($number[$i-1], $fromBase),bcpow($fromLen,$numberLen-$i)));
        return $retval;
    }
    if ($fromBaseInput != '0123456789')
        $base10=convBase($numberInput, $fromBaseInput, '0123456789');
    else
        $base10 = $numberInput;
		
    if ($base10<strlen($toBaseInput))
        return $toBase[$base10];
    while($base10 != '0')
    {
        $retval = $toBase[bcmod($base10,$toLen)].$retval;
        $base10 = bcdiv($base10,$toLen,0);
    }
    return $retval;
}


#Function to check if string contains special characters
function does_string_contain_special_characters($string, $allowSpaces=FALSE)
{
	if (!$allowSpaces && !preg_match("#^[a-zA-Z0-9]+$#", $string)) {
   		return TRUE;  
	} 
	else if ($allowSpaces && !preg_match("#^[a-zA-Z0-9 ]+$#", $string)) {
   		return TRUE;  
	} 
	else 
	{
   		return FALSE;
	}
}


#Function to clean a string and remove special characters or spaces
function remove_string_special_characters($string, $allowSpaces=FALSE)
{
	if($allowSpaces)
	{
		$string = str_replace(' ', '-', $string);
		return str_replace('-', ' ', preg_replace('/[^A-Za-z0-9\-]/', '', $string));
	}
	else
	{
		return preg_replace('/[^A-Za-z0-9]/', '', $string);
	}
}


# Format the date according to instructions given
function format_date($dateString, $instruction="YYYY-MM-DD H:I:S", $default="&nbsp;")
{
	$date = $dateString;
	# Proceed if the date is not empty
	if(!(empty($dateString) || $dateString == '0000-00-00 00:00:00' || $dateString == '0000-00-00'))
	{
		switch($instruction)
		{
			case "YYYY-MM-DD":
				$date = date("Y-m-d", strtotime($dateString));
			break;
			
			case "YYYY-MM-DD H:I:S":
				$date = date("Y-m-d H:i:s", strtotime($dateString));
			break;
			
			case "Y-m-d":
				$date = date("Y-m-d", strtotime($dateString));
			break;
			
			case "d-M-Y h:i:s":
			case "d-M-Y h:i:sa T":
			case "d-M-Y h:ia T":
			case "d-M-Y":
				$date = date($instruction, strtotime($dateString));
			break;
		}
	}
	else
	{
		$date = $default; 
	}
	
	return $date;
}







#Function to provide the difference of two dates in a desired format
#$minKey tells the function which minimum key to return in ideal situation, but if this key is empty, it will return the next non-empty key below it
function format_date_interval($startDate, $endDate, $returnArray=TRUE, $ignoreEmpty=TRUE, $minKey='')
{
    $interval = date_diff(date_create($startDate), date_create($endDate));
    $diffString = $interval->format("years:%Y,months:%M,days:%d,hours:%H,minutes:%i,seconds:%s");
    
	#Put the diff in an array
	$diffArray = array();
    array_walk(explode(',',$diffString),
    
	function($val,$key) use(&$diffArray){
        $diffPart=explode(':',$val);
        $diffArray[$diffPart[0]] = $diffPart[1];
    });
	
	#Remove the empty parts of the array
	$finalArray = array();
	foreach($diffArray AS $partKey=>$intervalPart)
	{
		$intervalPart = $intervalPart+0;
		if(!empty($intervalPart))
		{
			$finalArray[$partKey] = $intervalPart;
		}
	}
	
	#Now consider the minKey to be returned
	if(!empty($minKey))
	{
		$finalMinArray = array();
		$lastNonEmptyValue = 0;
		$reachedMinKey = FALSE;
			
		foreach($diffArray AS $key=>$value)
		{
			$value = 0+$value;
			#Only update the last non-empty value if you encounter a non-empty value
			$lastNonEmptyValue = !empty($value)? $value: $lastNonEmptyValue;
			
			if(!empty($value) && $key != $minKey)
			{
				$finalMinArray[$key] = $value;
				#Break if you already passed the minimum key
				if($reachedMinKey) break;
			} 
			else if(trim($key) == $minKey)
			{
				if(!empty($value))
				{
					$finalMinArray[$key] = $value;
				}
				$reachedMinKey = TRUE;
				if(!empty($lastNonEmptyValue))break;
			}
		}
		
		$finalArray = $finalMinArray;
	}
	
	
	#Return the interval in a desired format
	if($returnArray)
	{
		#Ignore empty parts of the interval or not?
		return $ignoreEmpty? $finalArray : $diffArray;
	}
	else
	{
		if($ignoreEmpty)
    	{
			$finalString = "";
			foreach($finalArray AS $partKey=>$intervalPart)
			{
				$intervalPart = 0+$intervalPart;
				$finalString .= $intervalPart." ".($intervalPart == 1? substr($partKey, 0, -1): $partKey).", ";
			}
			return !empty($finalArray)? trim($finalString, ', '): "0 seconds";
		}
		else 
		{
			return $diffString;
		}
	}
    
	
}









#Function to format a number to a desired length and format
function format_number($number, $maxCharLength=100, $decimalPlaces=2, $singleChar=TRUE, $hasCommas=TRUE)
{
	#first strip any formatting;
    $number = (0+str_replace(",","",$number));
    #is this a number?
    if(!is_numeric($number)) return false;
	
	#now format it based on desired length and other instructions
    if($number > 1000000000000 && $maxCharLength < 13) return number_format(($number/1000000000000),$decimalPlaces, '.', ($hasCommas? ',': '')).($singleChar? 'T': ' trillion');
    else if($number > 1000000000 && $maxCharLength < 10) return number_format(($number/1000000000),$decimalPlaces, '.', ($hasCommas? ',': '')).($singleChar? 'B': ' billion');
    else if($number > 1000000 && $maxCharLength < 7) return number_format(($number/1000000),$decimalPlaces, '.', ($hasCommas? ',': '')).($singleChar? 'M': ' million');
    else if($number > 1000 && $maxCharLength < 4) return number_format(($number/1000),$decimalPlaces, '.', ($hasCommas? ',': '')).($singleChar? 'K': ' thousand');
	else return number_format($number,(is_float($number)? $decimalPlaces: 0), '.', ($hasCommas? ',': ''));
}






#limit string length
function limit_string_length($string, $maxLength, $ignoreSpaces=TRUE, $endString='..')
{
    if (strlen(html_entity_decode($string, ENT_QUOTES)) <= $maxLength) return $string;
	
	if(!$ignoreSpaces)
	{
    	$newString = substr($string, 0, $maxLength);
		$newString = (substr($newString, -1, 1) != ' ')?substr($newString, 0, strrpos($newString, " ")) : $string;
	}
	else
	{
		$newString = substr(html_entity_decode($string, ENT_QUOTES), 0, $maxLength);
		if(strpos($newString, '&') !== FALSE)
		{
			$newString = substr($newString, 0, strrpos($newString, " "));
		}
	}
	
    return $newString.$endString;
}



#Function to compute distance between two latitudes and longitudes
function compute_distance_between_latitude_and_longitude($latitude1, $longitude1, $latitude2, $longitude2, $unit='miles')
{
	$theta = $longitude1 - $longitude2;
  	$distance = sin(deg2rad($latitude1)) * sin(deg2rad($latitude2)) +  cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta));
	$distance = acos($distance);
	$distance = rad2deg($distance);
	$miles = $distance * 60 * 1.1515;
	$unit = strtoupper($unit);
	 
	if ($unit == "kilometers") 
	{
	   return ($miles * 1.609344);
	} 
	else if ($unit == "nautical_miles") 
	{
	   return ($miles * 0.8684);
    } 
	else if ($unit == "miles")
	{
	   return $miles;
	}
}



#Function to compute age from birthday
function compute_age_from_birthday($birthday, $returnType='years')
{
	$age = 0;
	
	if(!empty($birthday) && $birthday != '0000-00-00')
	{
		$interval = format_date_interval($birthday, date('Y-m-d'), TRUE, FALSE);
		if($returnType == 'years')
		{
			$age = $interval['years'];
		}
		else
		{
			$age = $interval;
		}
	}
	
	return $age;
}



#Function to check whether a variable is not empty
function is_not_empty($variable)
{
	return !empty($variable);
}


# get a list of sort columns and their data to pass to array_multisort
function pick_sort_list_data($data, $dataKeys)
{
	$sortList = array();
	foreach($data AS $key=>$row)
	{
   	 	#Pick the columns to sort by
		foreach($dataKeys AS $dataKey)
		{
			$sortList[$dataKey][$key] = !empty($row[$dataKey])?$row[$dataKey]:'';
		}
	}
	
	return $sortList;
}



#Format website for display
function format_website_for_display($rawWebsite)
{
	$website = strtolower($rawWebsite);
	if(strpos($website, 'http://') !== false)
	{
		$website = substr($rawWebsite, 7);
	}
	else if(strpos($website, 'https://') !== false)
	{
		$website = substr($rawWebsite, 8);
	}
	#Do not show derivative URLs for source
	else if(strpos($website, '?') !== false || strlen($website) > 45)
	{
		$website = (strpos($website, '?') !== false && strlen($website) < 45)? $website: "";
	}
	else 
	{
		$website = $rawWebsite;
	}
	#Remove trailing slash if it is there
	$website = (substr($website, -1) == '/')? substr($website, 0, -1): $website;
	return strlen($website)> 45? "<a href='".$rawWebsite."' target='_blank'>".substr($website, 0,44).'..</a>': $website;
}



#Remove an array item from the given items and return the final array
function remove_item($item, $fullArray)
{
	#First remove the item from the array list
	unset($fullArray[array_search($item, $fullArray)]);
	
	return $fullArray;
}




#Return a string between the given strings
function get_string_between($string, $start, $end)
{
    $string = " ".$string;
    $ini = strpos($string,$start);
    if ($ini == 0) return "";
	
    $ini += strlen($start);
    $len = strpos($string,$end,$ini) - $ini;
	
    return substr($string,$ini,$len);
}



#Function to get a slow loading page link
function get_slow_link_url($url, $title, $loadingMessage='')
{
	return base_url().'page/load_slow_page/p/'.encrypt_value($url).'/t/'.encrypt_value($title).(!empty($loadingMessage)? '/m/'.encrypt_value($loadingMessage): '');
}



#Function to get the longitude and latitude of a location given its address
function get_longitude_latitude_from_address($address)
{
	$location = array('longitude'=>'', 'latitude'=>'');
	#Remove any trailing spaces
	$address = trim($address);
	
	if(!empty($address))
	{
		$geocode=file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.str_replace(' ','+',$address).'&sensor=false');
        $output= json_decode($geocode);
        $location['latitude'] = !empty($output->results[0]->geometry->location->lat)? $output->results[0]->geometry->location->lat: '';
        $location['longitude'] = !empty($output->results[0]->geometry->location->lng)? $output->results[0]->geometry->location->lng: '';
	}
	
	return $location;
}



#Function to get unique array values from a multidimensional array
function multi_array_unique($array)
{
	return array_map("unserialize", array_unique(array_map("serialize", $array)));
}




#CHecks whether an array key that begins or ends in a value is in the passed array
function array_key_contains($keyPart, $array)
{
	$keys = array_keys($array);
	$theKey = '';
	$exists = FALSE;
	
	foreach($keys AS $key)
	{
		if(strpos($key, $keyPart) !== FALSE)
		{
			$exists = TRUE;
			$theKey = $key;
			break;
		}
	}
	
	return array('boolean'=>$exists, 'key'=>$theKey);
}





# Generates an 8-character temporary password for the user - this is a one time case and system does not keep un-encrypted copy
function generate_temp_password()
{
	$numbers = '0123456789';
	$letters = 'abcdefghijklmnopqrstuvwxyz';
	$characters = '_!-*.';
	$time = strtotime('now');
	
	$password = array();
	$password[0] = $letters[rand(0, strlen($letters)-1)];
	$password[1] = $letters[rand(0, strlen($letters)-1)];
	$password[2] = $numbers[rand(0, strlen($numbers)-1)];
	$password[3] = $characters[rand(0, strlen($characters)-1)];
	$password[4] = $time[rand(0, strlen($time)-1)];
	$password[5] = strtoupper($letters[rand(0, strlen($letters)-1)]);
	$password[6] = $letters[rand(0, strlen($letters)-1)];
	$password[7] = $time[rand(0, strlen($time)-1)];
	
	return implode('',$password);
}



# Generate a verification code for a new person
function generate_person_code($id)
{
	return strrev(strtoupper(generate_random_bytes(2).dechex($id)));
}




# Get the row in a multi dimensional array that has a specified key set as the given value
function get_row_from_list($list, $key, $value, $return='value')
{
	$selected = array();
	foreach($list AS $i=>$row)
	{
		if(array_key_exists($key, $row) && $row[$key] == $value){
			$selected = ($return == 'key')? $i: $row; 
			break;
		}
	}
	
	return $selected;
}


# Get the first page to hit when logged in
function get_user_dashboard($obj, $userId)
{
	# 1. Has the system set a default page to redirect to?
	if($obj->native_session->get('redirect_url'))
	{
		$page = $obj->native_session->get('redirect_url');
		$obj->native_session->delete('redirect_url');
	} 
	else
	{
		# 2. Get the user group
		if($obj->native_session->get('__permission_group'))
		{
			$groupId = $obj->native_session->get('__permission_group');
		}
		else
		{
			$user = $obj->_query_reader->get_row_as_array('get_user_by_id', array('user_id'=>$userId));
			$groupId = $user['permission_group_id'];
		}
	
		# 3. Get the group default page
		if($obj->native_session->get('__group_default_page'))
		{
			$page = $obj->native_session->get('__group_default_page');
		}
		else if($obj->native_session->get('__permissions'))
		{
			#Go to the group default page if allowed
			$default = $obj->_query_reader->get_row_as_array('get_group_default_permission', array('group_id'=>$groupId));
			if(!empty($default['code']) && in_array($default['code'], $obj->native_session->get('__permissions')))
			{
				$page = $default['page'];
			}
			# 4. If the user is not allowed to view default page, go to the first allowed permission
			else 
			{
				$permissions = $obj->native_session->get('__permissions');
				$permission = $obj->_query_reader->get_row_as_array('get_permission_by_code', array('code'=>$permissions[0]));
				$page = !empty($permission['url'])? $permission['url']: "";
			}
			
			# Set this so that you do not have to fetch the default page from the DB again - for this user's session
			if(!empty($page)) $obj->native_session->set('__group_default_page', $page);
		}
		
		# 5. If none, logout the user and notify
		if(empty($page))
		{
			$page = 'account/logout';
			$obj->native_session->set('msg', 'ERROR: Your account does not have any access permissions.');
		}
	}
	
	return $page;
}



# Get the message stored in the session to be shown at the given area
function get_session_msg($obj)
{
	$msg = $obj->native_session->get('msg')? $obj->native_session->get('msg'): "";
	$obj->native_session->delete('msg');
	
	return $msg;
}



# Check user access to a given feature
# Valid return options [msg, boolean]
function check_access($obj, $accessCode, $return='msg', $setMenuItem=true)
{
	# 1. Are the user's permissions set and they have the requested permission?
	# then, return appropriate response
	if($obj->native_session->get('__permissions') && in_array($accessCode, $obj->native_session->get('__permissions')))
	{
		if($setMenuItem) $obj->native_session->set('__selected_permission', $accessCode);
		if($return == 'boolean') return true;
	}
	else
	{
		if($return == 'boolean')
		{
			return false;
		}
		else
		{
			$obj->native_session->set('msg', "ERROR: You do not have access to this feature.");
			redirect(base_url().($obj->native_session->get('__user_id')? get_user_dashboard($obj, $obj->native_session->get('__user_id')): 'account/logout')); 
		}
	}
}



# Choose the right permission access code to return to the function - up to 2 levels
function get_access_code($data, $instructions)
{
	$code = '';
	
	# $key = 'action', $value = full array for action permissions
	foreach($instructions AS $key=>$value)
	{
		# 'action' is passed in the data
		if(array_key_exists($key, $data))
		{
			# Check if this is an array - which requires further processing
			if(!empty($value[$data[$key]]) && is_array($value[$data[$key]]))
			{
				# Loop through 'level' array
				foreach($value[$data[$key]] AS $key2=>$value2)
				{
					# Level exists
					if(array_key_exists($key2, $data))
					{
						$code = $value2[$data[$key2]];
						break 2;
					}
				}
			}
			#Code is available at the first level
			else if(!empty($value[$data[$key]]))
			{
				$code = $value[$data[$key]];
				break;
			}
		}
	}
	
	#Handle a unique case where a default access code for the function is provided
	if(empty($code) && array_key_exists('', $instructions))
	{
		$code = $instructions[''];
	}
	
	
	return $code;
}



#Check if a model is loaded 
function is_model_loaded($obj, $modelName) 
{
	return in_array($modelName, $obj->ci_models, TRUE);
}



#Process the other field for data
function process_other_field($data)
{
	if(!empty($data['other']))
	{
		$level1Parts = explode('|', $data['other']);
		foreach($level1Parts AS $part)
		{
			if(!empty($part))
			{
				$level2Parts = explode('=', $part);
				if(count($level2Parts) > 1) $data[$level2Parts[0]] = restore_bad_chars($level2Parts[1]);
			}
		}
	}
	
	return $data;
}




# Force file download
function force_download($folder, $file)
{
	if(file_exists(UPLOAD_DIRECTORY.$folder."/".$file))
	{
		if(strtolower(strrchr($file,".")) == '.pdf')
		{
			header('Content-disposition: attachment; filename="'.$file.'"');
			header('Content-type: application/pdf');
			readfile(UPLOAD_DIRECTORY.$folder."/".$file);
		}
		if(strtolower(strrchr($file,".")) == '.zip')
		{
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Description: File Transfer');
			header('Content-Disposition: attachment; filename="'.strtotime('now').str_replace('.','',get_ip_address()).'.zip"');
			header('Content-Transfer-Encoding: binary');
			header('Vary: Accept-Encoding');
			header('Content-Encoding: gzip');
			header('Keep-Alive: timeout=5, max=100');
			header('Connection: Keep-Alive');
			header('Transfer-Encoding: chunked');
			header('Content-Type: application/octet-stream');
			apache_setenv('no-gzip', '1');

		}
		else
		{
			redirect(base_url()."assets/uploads/".$folder."/".$file);
		}
	}
}




# Send download headers
function send_download_headers($filename) 
{
    # disable caching
    $now = gmdate("D, d M Y H:i:s");
    header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
    header("Last-Modified: {$now} GMT");

    # force download  
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");

    # disposition / encoding on response body
    header("Content-Disposition: attachment;filename={$filename}");
    header("Content-Transfer-Encoding: binary");
}



# Convert an array to csv
function array2csv(array &$array)
{
   if(count($array) == 0) 
   {
     return null;
   }
   ob_start();
   $df = fopen("php://output", 'w');
   fputcsv($df, array_keys(reset($array)));
   
   foreach($array AS $row) 
   {
      fputcsv($df, $row);
   }
   fclose($df);
   return ob_get_clean();
}




# Minify a list of files
function minify_js($page, $files) 
{
	$string = "";
	# Minify and show the minified version
	if(MINIFY)
	{
		$fileLocation = HOME_URL.'assets/js/';
		# If the file exists, just return the file, else create the minified version
		if(!file_exists($fileLocation.'__'.$page.'.min.js'))
		{
			require_once(HOME_URL.'external_libraries/jsmin/JSMin.php');
			foreach($files AS $file)
			{
				$min = JSMin::minify(file_get_contents($fileLocation.$file));
  				file_put_contents($fileLocation.'__'.$page.'.min.js', $min, FILE_APPEND);
			}
		}
		$string = "<script type='text/javascript' src='".base_url()."assets/js/__".$page.".min.js'></script>"; 
	}
	# List the files out one by one
	else
	{
		foreach($files AS $file) $string .= "<script type='text/javascript' src='".base_url()."assets/js/".$file."'></script>";
	}
	
	return $string;
}


# Function to redirect a user from an iframe
function redirectFromIframe($url)
{
	echo "<script type='text/javascript'>window.top.location.href = '".$url."';</script>";exit;
}


# Format age for display indicator
function format_age($userAge, $return='style', $retirementAge = RETIREMENT_AGE)
{
	$format = "";
	
	if(($retirementAge - $userAge) < 4 && $userAge <= RETIREMENT_AGE)
	{
		$format = $return=='timeleft'? "<br>[".($retirementAge - $userAge)." yrs to AMAR]": "font-weight:bold;color: #FFD418;";
	}
	else if(($retirementAge - $userAge) < 0)
	{
		$format = $return=='timeleft'? "<br>[".($userAge - $retirementAge)." yrs past AMAR]": "font-weight:bold;color: #FF0000;";
	}
	
	return $format;
}



?>