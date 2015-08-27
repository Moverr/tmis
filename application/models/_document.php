<?php
/**
 * Handles document uploads and tracking.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 01/08/2015
 */
class _document extends CI_Model
{
	

	# Generate a letter with the passed details
	function generate_letter($code, $details, $action='save', $return='filename')
	{
		/*
		print_r($code);
		echo "Inside GEnerate Letter";
		print_r($details);
		exit();   */
	#	if(!empty($details))
		# 1. Generate the letter name
		$letterUrl = 'file_'.strtotime('now').'.pdf';
		$location = UPLOAD_DIRECTORY.'documents/';
		
		# 2. Load the letter details from the database
		$template = $this->get_template_by_code($code);
	#	print_r($template); exit();
		# 3. Generate the document from the template
		$document = $this->populate_template($template, $details);
		
		# 4. Check if there are settings for the document to be generated
		$documentSettings = array('size'=>'A4','orientation'=>'portrait');
		if(!empty($details['document_size'])) $documentSettings['size'] = $details['document_size'];
		if(!empty($details['document_orientation'])) $documentSettings['orientation'] = $details['document_orientation'];
		$this->generate_pdf($document, $location.$letterUrl, $action, $documentSettings);
		
		# If the file is created, then return the file name, else, just an empty string
		return file_exists($location.$letterUrl)? ($return == 'url'? $location.$letterUrl: $letterUrl): '';
	}
	
	
	
	
	
	# Generate a PDF document
	function generate_pdf($document, $url, $action, $paperSetting=array('size'=>'A4','orientation'=>'portrait'))
	{
		# get the external library that generates the PDF
		require_once(HOME_URL."external_libraries/dompdf/dompdf_config.inc.php");

		# Strip slashes if this PHP version supports get_magic_quotes
		$document = get_magic_quotes_gpc()? stripslashes($document): $document;
		
		$dompdf = new DOMPDF();
		$dompdf->load_html($document);
		$dompdf->set_paper($paperSetting['size'], $paperSetting['orientation']);
		$dompdf->render();
	
		# Store the entire PDF as a string in $pdf
		$pdf = $dompdf->output();
		# Write $pdf to disk
		file_put_contents($url, $pdf);

		# If the user wants to download the file, then stream it; otherwise display it in the browser as is.
		if($action == 'download')
		{
			$dompdf->stream($filename, array("Attachment" => true));
			exit(0);
		}
	}
	
	
	
			
	
	
	# Get a template of the document given its code
	function get_template_by_code($code)
	{
		return $this->_query_reader->get_row_as_array('get_message_template', array('message_type'=>$code));
	}	
				
	
	
	# Populate the template to generate the actual document content
	function populate_template($template, $values=array())
	{
		$document = "";
		if(!empty($template['details']))
		{
			# Order keys by length - longest first
			array_multisort(array_map('strlen', array_keys($values)), SORT_DESC, $values);
			
			# go through all passed values and replace where they appear in the template text
			foreach($values AS $key=>$value)
			{
				$template['details'] = str_replace('_'.strtoupper($key).'_', html_entity_decode($value, ENT_QUOTES), $template['details']);
			}
			$document = $template['details'];
		}
		
		return $document;
	}
	
	
	
	
	
	
	
	
	# Upload a file on the system
	function upload($fileObj, $instructions=array('type'=>'document'))
	{
		$boolean = false;
		$msg = $file = '';
		$extension = pathinfo($fileObj['name'], PATHINFO_EXTENSION);
		
		$imageTypes = array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_SWF, IMAGETYPE_PSD, IMAGETYPE_BMP, IMAGETYPE_TIFF_II, IMAGETYPE_TIFF_MM, IMAGETYPE_JPC, IMAGETYPE_JP2, IMAGETYPE_JPX, IMAGETYPE_JB2, IMAGETYPE_SWC, IMAGETYPE_IFF, IMAGETYPE_WBMP, IMAGETYPE_XBM, IMAGETYPE_ICO);
		$documentTypes = array('application/zip', 'application/x-zip', 'application/x-zip-compressed','application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/vnd.openxmlformats-officedocument.presentationml.presentation','application/pdf', 'application/msword', 'application/rtf', 'application/vnd.ms-powerpoint', 'application/vnd.oasis.opendocument.text','application/vnd.oasis.opendocument.spreadsheet','text/plain');
		
		# Proceed based on the type of file uploaded
		if($instructions['type'] == 'image' && in_array(exif_imagetype($fileObj['tmp_name']), $imageTypes))
		{
			$file = $this->move_file($fileObj['tmp_name'], 'images', $extension);
		}
		# TODO: Check for valid formats - not caught by name extension check
		else if($instructions['type'] == 'document')# && in_array($this->get_mime($fileObj['tmp_name']), $documentTypes))
		{
			$file = $this->move_file($fileObj['tmp_name'], 'documents', $extension);
		}
		else 
		{
			$msg = "WARNING: The uploaded file format is not supported.";
		}
		
		return array('boolean'=>$boolean, 'file'=>$file, 'msg'=>$msg);
	}
	
	
	# Actually moves the uploaded file to its destination
	function move_file($tempFileObj, $uploadFolder, $extension)
	{
		$file = '';
		$destinationFolder = UPLOAD_DIRECTORY.$uploadFolder.'/';
		
		# Create the directory if it does not exist
		if (!empty($uploadFolder) && !file_exists($destinationFolder)) mkdir($destinationFolder, 0777);
  		
		#Move the file to its new location
		if(!empty($uploadFolder))
		{
			 $file = 'file_'.strtotime('now').'.'.$extension;
			 # unmask makes the privildges of the uploaded file to be the default permissions
			 if(move_uploaded_file($tempFileObj, $destinationFolder.$file)) umask(0);
			 else $file = '';
		}
		
		return $file;
	}
	
	
	
	
	function get_mime($url) 
	{
    	$image_type = exif_imagetype($url);
    	return image_type_to_mime_type($image_type);
	}

}


?>