<?php
/**
 * This class handles functions related to communication carriers in the system.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 01/08/2015
 */
class _carrier extends CI_Model
{
	

	# Get the email domain of a carrier for use in sending an email-to-sms message
	function get_email_domain($telephone)
	{
		$domain = $this->_query_reader->get_row_as_array('get_carrier_email_domain', array('number_stub'=>substr($telephone, 0,3) ));
		return !empty($domain['email_domain'])? $domain['email_domain']: '';
	}

}


?>