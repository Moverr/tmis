<?php
/*
 * This document includes all global settings required for operation of the system
 *
 */



/*
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *     development
 *     testing
 *     production
 *
 * NOTE: If you change these, also change the error_reporting() code below
 */
	define('ENVIRONMENT', 'development');


/*
 *---------------------------------------------------------------
 * GLOBAL SETTINGS
 *---------------------------------------------------------------
 */
	define('SECURE_MODE', FALSE);

	define('BASE_URL', 'http://localhost/tmis/');#Set to HTTPS:// if SECURE_MODE = TRUE

	define('RETRIEVE_URL_DATA_IGNORE', 3);#The starting point to obtain the passed url data

	define('SITE_TITLE', "TMIS");

	define('SITE_SLOGAN', "");

	define('SYS_TIMEZONE', "Africa/Nairobi");

	define('NUM_OF_ROWS_PER_PAGE', "5");

	define('NUM_OF_LISTS_PER_VIEW', "10");

	define('IMAGE_URL', BASE_URL."assets/images/");

	define('HOME_URL', getcwd()."/");

	define('DEFAULT_CONTROLLER', 'page');

	define('UPLOAD_DIRECTORY',  HOME_URL."assets/uploads/");

	define('MAX_FILE_SIZE', 40000000);

	define('ALLOWED_EXTENSIONS', ".doc,.docx,.txt,.pdf,.xls,.xlsx,.jpeg,.png,.jpg,.gif");

	define('MAXIMUM_FILE_NAME_LENGTH', 100);

	define("MINIFY", FALSE);

	define('PORT_HTTP', '80');

  	define('PORT_HTTP_SSL', '443');

	define('PHP_LOCATION', "php5");

 	define('ENABLE_PROFILER', FALSE); #See perfomance stats based on set benchmarks

	define('DOWNLOAD_LIMIT', 10000); #Max number of rows that can be downloaded

	define('RETIREMENT_AGE', 60); # Mandatory retirement age







/*
 *---------------------------------------------------------------
 * CRON JOB SETTINGS
 *---------------------------------------------------------------
 */

	define('CRON_HOME_URL', "/var/www/tmis/");

	define('CRON_FILE', CRON_HOME_URL."cron.list");

	define('CRON_FILE_NAME', "cron.list");

	define('CRON_FILE_LOG', CRON_HOME_URL."cron.log");

	define('CRON_REFRESH_PERIOD', "5 minutes");

	define('DEFAULT_CRON_HOME_URL', "/var/www/tmis/");

	define('GLOBAL_CRON_FILE', DEFAULT_CRON_HOME_URL."global.cron.list");

	define("CRON_INSTALLATIONS", serialize(array('/var/www/tmis/'))); #Use in case of multiple system installations on one server





/*
 *---------------------------------------------------------------
 * QUERY CACHE SETTINGS
 *---------------------------------------------------------------
 */

	define('ENABLE_QUERY_CACHE', FALSE);

 	define('QUERY_FILE', HOME_URL.'application/helpers/queries_list_helper.php');





/*
 *---------------------------------------------------------------
 * MESSAGE CACHE SETTINGS
 *---------------------------------------------------------------
 */

	define('ENABLE_MESSAGE_CACHE', FALSE);

 	define('MESSAGE_FILE', HOME_URL.'application/helpers/message_list_helper.php');




/*
 *---------------------------------------------------------------
 * SMS GLOBAL CREDENTIALS
 *---------------------------------------------------------------
 */

	define('SMS_GLOBAL_USERNAME', 'sms-global-api-user');

 	define('SMS_GLOBAL_PASSWORD', 'sms-global-api-pass');

 	define('SMS_GLOBAL_VERIFIED_SENDER', 'verified-phone-number-with-country-code');








/*
 *
 *	0 = Disables logging, Error logging TURNED OFF
 *	1 = Error Messages (including PHP errors)
 *	2 = Debug Messages
 *	3 = Informational Messages
 *	4 = All Messages
 *	The log file can be found in: [HOME_URL]application/logs/
 *	Run >tail -n50 log-YYYY-MM-DD.php to view the errors being generated
 */
	define('LOG_ERROR_LEVEL', 0);


/*
 *---------------------------------------------------------------
 * COMMUNICATION SETTINGS
 *---------------------------------------------------------------
 */

	define("NOREPLY_EMAIL", "noreply@tmis.go.ug");

	define("APPEALS_EMAIL", "appeals@tmis.go.ug");

	define("FRAUD_EMAIL", "fraud@tmis.go.ug");

	define("SECURITY_EMAIL", "security@tmis.go.ug");

	define("HELP_EMAIL", "support@tmis.go.ug");

	define('SITE_ADMIN_MAIL', "azziwa@gmail.com");

	define("SIGNUP_EMAIL", "register@tmis.go.ug");

	define('SITE_ADMIN_NAME', "TMIS Admin");

	define('SITE_GENERAL_NAME', "TMIS");

	define('DEV_TEST_EMAIL', "azziwa@gmail.com");



/*
 *--------------------------------------------------------------------------
 * URI PROTOCOL
 *--------------------------------------------------------------------------
 *
 * The default setting of "AUTO" works for most servers.
 * If your links do not seem to work, try one of the other delicious flavors:
 *
 * 'AUTO'
 * 'REQUEST_URI'
 * 'PATH_INFO'
 * 'QUERY_STRING'
 * 'ORIG_PATH_INFO'
 *
 */

	define('URI_PROTOCOL', 'AUTO'); // Set "AUTO" For WINDOWS
									       // Set "REQUEST_URI" For LINUX

/*
 *---------------------------------------------------------------
 * DATABASE SETTINGS
 *---------------------------------------------------------------
 */

	define('HOSTNAME', "localhost");

	define('USERNAME', "root");

	define('PASSWORD', "");

	define('DATABASE', "tmis");

	define('DBDRIVER', "mysqli");

	define('DBPORT', "3306");




/*
 *---------------------------------------------------------------
 * EMAIL SETTINGS
 *---------------------------------------------------------------
 */
	define('SMTP_HOST', "localhost");

	define('SMTP_PORT', "25");

	define('SMTP_USER', "root");

	define('SMTP_PASS', "");

	define('FLAG_TO_REDIRECT', "0");// 1 => Redirect emails to a specific mail id,
									// 0 => No need to redirect emails.
/*
 * If "FLAG_TO_REDIRECT" is set to 1, it will redirect all the mails from this site
 * to the email address  defined in "MAILID_TO_REDIRECT".
 */

	define('MAILID_TO_REDIRECT', DEV_TEST_EMAIL);
?>
