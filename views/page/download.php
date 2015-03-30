<?php 

if(!empty($area) && $area == 'download_csv')
{
	send_download_headers("file_".strtotime('now').".csv");
	echo array2csv($list);
	die();
}

else if(!empty($area) && $area == 'download_shortlist_csv')
{
	$htmldata = array(
		array('Shortlist:'=>'School:', $shortlist_name=>$vacancy_details['institution_name']),
		array('Shortlist:'=>'Vacancy:', $shortlist_name=>$vacancy_details['topic']),
		array('Shortlist:'=>'Role:', $shortlist_name=>$vacancy_details['role_name']),
		array('Shortlist:'=>'Summary:', $shortlist_name=>$vacancy_details['summary']),
		array('Shortlist:'=>'', $shortlist_name=>''),
		array('NAME','NUMBER','ADDRESS')
	);
	
	foreach($list AS $row)
	{
		array_push($htmldata, array($row['name'], $row['number'], $row['address'])); 
	}
	
	send_download_headers("file_".strtotime('now').".csv");
	echo array2csv($htmldata);
	die();
}

?>