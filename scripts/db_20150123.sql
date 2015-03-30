
-- Update the vacancy status to reflect the approval chain steps
-- ----------------------------------------------------------------

ALTER TABLE `vacancy` CHANGE `status` `status` ENUM( 'saved', 'verified', 'published', 'archived' ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'saved';







-- Approval Chain DB tables
-- ----------------------------------------------------------------

CREATE TABLE approval_chain (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  chain_type varchar(100) NOT NULL,
  step_number varchar(10) NOT NULL,
  subject_id varchar(100) NOT NULL,
  originator_id varchar(100) NOT NULL,
  approver_id varchar(500) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  date_created datetime NOT NULL,
  last_updated datetime NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;




CREATE TABLE approval_chain_scope (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  approver varchar(100) NOT NULL,
  scope varchar(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

INSERT INTO approval_chain_scope (id, approver, scope) VALUES
(1, 'applicant', ''),
(2, 'teacher', 'self'),
(3, 'data', 'self'),
(4, 'manager', 'institution'),
(5, 'cao', 'county'),
(6, 'deo', 'district'),
(7, 'dsc', 'district'),
(8, 'mops', 'country'),
(9, 'moes', 'country'),
(10, 'psc', 'country'),
(11, 'hr', 'country'),
(12, 'tiet', 'country'),
(13, 'admin', 'system');



CREATE TABLE approval_chain_setting (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  chain_type varchar(100) NOT NULL,
  step_number int(11) NOT NULL,
  originators varchar(300) NOT NULL,
  approvers varchar(300) NOT NULL,
  step_actions varchar(500) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

INSERT INTO approval_chain_setting (id, chain_type, step_number, originators, approvers, step_actions) VALUES
(1, 'vacancy', 1, '', 'admin,moes,deo', 'notify_next_chain_party'),
(2, 'vacancy', 2, 'admin,moes,deo', 'mops', 'notify_previous_and_next_chain_parties'),
(3, 'vacancy', 3, 'mops', 'admin,esc', 'publish_job_notice,notify_previous_chain_parties'),
(4, 'confirmation', 1, '', 'teacher', 'notify_next_chain_party'),
(5, 'confirmation', 2, 'teacher', 'moes,cao', 'issue_confirmation_letter,notify_previous_and_next_chain_parties'),
(6, 'confirmation', 3, 'moes,cao', 'esc,dsc', 'notify_previous_chain_parties'),
(7, 'registration', 1, '', 'admin,data,hr,teacher', 'notify_next_chain_party'),
(8, 'registration', 2, 'admin,data,hr,teacher', 'hr', 'issue_file_number,notify_previous_and_next_chain_parties'),
(9, 'registration', 3, 'hr', 'tiet', 'issue_registration_certificate,notify_previous_chain_parties'),
(10, 'transfer', 1, '', 'teacher,manager', 'notify_next_chain_party'),
(11, 'transfer', 2, 'teacher,manager', 'manager', 'notify_previous_and_next_chain_parties'),
(12, 'transfer', 3, 'manager', 'moes,cao', 'issue_transfer_letter,notify_previous_and_next_chain_parties'),
(13, 'transfer', 4, 'moes,cao', 'manager', 'submit_transfer_pca,notify_previous_and_next_chain_parties'),
(14, 'transfer', 5, 'manager', 'psc', 'confirm_transfer,notify_previous_chain_parties'),
(15, 'leave', 1, '', 'teacher,manager', 'notify_next_chain_party'),
(16, 'leave', 2, 'teacher,manager', 'moes,cao', 'notify_previous_and_next_chain_parties'),
(17, 'leave', 3, 'moes,cao', 'esc,dsc', 'notify_previous_and_next_chain_parties'),
(18, 'leave', 4, 'esc,dsc', 'moes,cao', 'send_verification_letter,notify_previous_chain_parties'),
(19, 'retirement', 1, '', 'teacher,manager', 'notify_next_chain_party'),
(20, 'retirement', 2, 'teacher,manager', 'manager', 'confirm_retirement,notify_previous_chain_parties'),
(21, 'secrecy', 1, '', 'teacher', 'notify_next_chain_party'),
(22, 'secrecy', 2, 'teacher', 'admin,esc,dsc', 'apply_data_secrecy,notify_previous_chain_parties'),
(23, 'data', 1, '', 'admin,data', 'notify_next_chain_party'),
(24, 'data', 2, 'admin,data', 'admin', 'activate_data_records,notify_previous_chain_parties'),
(25, 'recommendation', 1, '', 'manager', 'notify_next_chain_party'),
(26, 'recommendation', 2, 'manager', 'esc,dsc', 'notify_previous_chain_parties'),
(27, 'census', 1, '', 'manager', 'notify_next_chain_party'),
(28, 'census', 2, 'manager', 'moes,cao', 'notify_previous_chain_parties');




ALTER TABLE `approval_chain` ADD `actual_approver` VARCHAR( 100 ) NOT NULL AFTER `approver_id` ;
ALTER TABLE `approval_chain` ADD `comment` VARCHAR( 500 ) NOT NULL AFTER `status` ;


ALTER TABLE `person` ADD `is_visible` ENUM( 'Y', 'N' ) NOT NULL DEFAULT 'Y' AFTER `citizenship_type` ;


ALTER TABLE `message_exchange` CHANGE `category` `subject` VARCHAR( 300 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
CHANGE `sender_id` `sender` VARCHAR( 100 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ;



UPDATE `permission` SET `url` = 'user/add' WHERE `permission`.`id` =59;

ALTER TABLE `person` ADD `signature` VARCHAR( 300 ) NOT NULL AFTER `is_visible` ;

ALTER TABLE `user` CHANGE `status` `status` ENUM( 'pending', 'completed', 'active', 'inactive', 'blocked' ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'pending';







--
-- Table structure for table 'carrier'
--

DROP TABLE IF EXISTS carrier;
CREATE TABLE carrier (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(300) NOT NULL,
  displayed_name varchar(300) NOT NULL,
  country_code varchar(10) NOT NULL,
  sms_email_domain varchar(300) NOT NULL,
  mms_email_domain varchar(300) NOT NULL,
  number_stubs text NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

--
-- Dumping data for table 'carrier'
--

INSERT INTO carrier (id, name, displayed_name, country_code, sms_email_domain, mms_email_domain, number_stubs) VALUES
(1, 'mtn', 'MTN Uganda', '256', 'mtn.co.ug', 'mtn.co.ug', '077,078'),
(2, 'airtel', 'Airtel Uganda', '256', '', '', '070,075'),
(3, 'utl', 'Uganda Telecom', '256', '', '', '071'),
(4, 'orange', 'Orange Uganda', '256', '', '', '079'),
(7, 'k2', 'K2 Telecom', '256', '', '', '073'),
(8, 'smart', 'Smart Telecom Uganda', '256', '', '', '074'),
(9, 'vodafone', 'Vodafone Uganda', '256', '', '', '076');

-- --------------------------------------------------------

--
-- Table structure for table 'message'
--

DROP TABLE IF EXISTS message;
CREATE TABLE message (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  message_type varchar(300) NOT NULL,
  `subject` varchar(300) NOT NULL,
  details text NOT NULL,
  sms varchar(300) NOT NULL,
  copy_admin enum('Y','N') NOT NULL DEFAULT 'Y',
  date_added datetime NOT NULL,
  added_by varchar(100) NOT NULL,
  last_updated datetime NOT NULL,
  last_updated_by varchar(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

--
-- Dumping data for table 'message'
--

INSERT INTO message (id, message_type, subject, details, sms, copy_admin, date_added, added_by, last_updated, last_updated_by) VALUES
(1, 'new_teacher_first_step', 'Your Verification Code for TMIS', 'Hi _FIRST_NAME_,\r\n<br>\r\n<br>A profile has been created for you at the Teacher Management Information System (TMIS). You will need this code to confirm your account:\r\n<br>_VERIFICATION_CODE_\r\n<br>\r\n<br>In case you closed the TMIS website, go to the link below to continue with your registration:\r\n<br>_LOGIN_LINK_\r\n<br>You will need the following details to login:\r\n<br>\r\nYour user name: _EMAILADDRESS_\r\n<br>Your Temporary Password: _PASSWORD_\r\n<br>\r\n<br>Regards,\r\n<br>Your TMIS Admin Team\r\n<br>', 'Your TMIS profile has been created. Confirm your account at _LOGIN_LINK_ with code: _VERIFICATION_CODE_ Login with username: _EMAILADDRESS_ and password: _PASSWORD_', 'Y', '2015-01-14 00:00:00', '1', '0000-00-00 00:00:00', ''),
(2, 'teacher_application_submitted', 'Your TMIS Application Has Been Submitted', 'Hi _FIRST_NAME_,\r\n<br>\r\n<br>Your TMIS application has been submitted for review. You will be notified by email when it is approved for you to begin enjoying the benefits of a TMIS registered teacher.\r\n<br>\r\n<br>In case you closed the TMIS website, go to the link below to learn more about TMIS:\r\n<br>_LOGIN_LINK_\r\n<br>\r\n<br>Regards,\r\n<br>Your TMIS Admin Team\r\n<br>', 'Your TMIS application has been submitted for review.', 'Y', '2015-01-16 00:00:00', '1', '2015-01-16 00:00:00', '1'),
(3, 'notify_next_chain_party', 'Your Approval at TMIS is Required By _ORIGINATOR_NAME_', 'Hi _FIRST_NAME_,\r\n<br>\r\n<br>Your approval at TMIS is required by _ORIGINATOR_NAME_ for a _ITEM_TYPE_. This was requested on _ACTION_DATE_.\r\n<br>\r\n<br>Regards,\r\n<br>Your TMIS Admin Team\r\n<br>', 'Your TMIS approval is required by _ORIGINATOR_NAME_ for a _ITEM_TYPE_.', 'Y', '2015-01-23 00:00:00', '1', '2015-01-23 00:00:00', '1'),
(4, 'notify_previous_chain_parties', 'A _ITEM_TYPE_ has been _VERIFICATION_RESULT_ by _APPROVER_NAME_', 'Hi _FIRST_NAME_,\r\n<br>\r\n<br>The _ITEM_TYPE_ you acted on at TMIS has been _VERIFICATION_RESULT_ by _APPROVER_NAME_. This action was taken on _ACTION_DATE_. This is the final action on your request.\r\n<br>\r\n<br>Regards,\r\n<br>Your TMIS Admin Team\r\n<br>', 'The _ITEM_TYPE_ you acted on at TMIS has been _VERIFICATION_RESULT_ by _APPROVER_NAME_. This is the final action.', 'Y', '2015-01-23 00:00:00', '1', '2015-01-23 00:00:00', '1'),
(5, 'document__confirmation_letter', '', 'This is the confirmation letter to be sent to _FIRST_NAME_.', '', 'Y', '2015-01-24 00:00:00', '1', '2015-01-24 00:00:00', '1'),
(6, 'issue_file_number', 'You Have Been Issued a Teacher File Number', 'Hi _FIRST_NAME_,\r\n<br>\r\n<br>You have been issued a file number: _FILE_NUMBER_.\r\n<br>\r\n<br>Use it in any of your correspondence with the ministry to allow us to process your case faster.\r\n<br>\r\n<br>Regards,\r\n<br>Your TMIS Admin Team\r\n<br>\r\n', 'You have been issued a file number: _FILE_NUMBER_. Use it in any of your correspondence with us.', 'Y', '2015-01-24 00:00:00', '1', '2015-01-24 00:00:00', '1'),
(7, 'send_confirmation_letter', 'Your Confirmation Letter', 'Hi _FIRST_NAME_,\r\n<br>\r\n<br>You have been issued a confirmation letter for your application which is attached. You can also access it in the TMIS system using your account.\r\n<br>\r\n<br>Regards,\r\n<br>Your TMIS Admin Team\r\n<br>', 'Your confirmation letter has been sent.', 'Y', '2015-01-24 00:00:00', '1', '2015-01-24 00:00:00', '1'),
(8, 'document__registration_certificate', '', 'This is the registration certificate to be sent to _FIRST_NAME_.', '', 'Y', '2015-01-24 00:00:00', '1', '2015-01-24 00:00:00', '1'),
(9, 'send_registration_certificate', 'Your Registration Certificate', 'Hi _FIRST_NAME_,\r\n<br>\r\n<br>You have been issued a registration certificate for your application which is attached. You can also access it in the TMIS system using your account.\r\n<br>\r\n<br>Regards,\r\n<br>Your TMIS Admin Team\r\n<br>', 'Your registration certificate has been sent.', 'Y', '2015-01-24 00:00:00', '1', '2015-01-24 00:00:00', '1'),
(10, 'document__transfer_letter', '', 'This is the transfer letter to be sent to _FIRST_NAME_.', '', 'Y', '2015-01-24 00:00:00', '1', '2015-01-24 00:00:00', '1'),
(11, 'send_transfer_letter', 'Your Transfer Letter', 'Hi _FIRST_NAME_,\r\n<br>\r\n<br>You have been issued a transfer letter for your application which is attached. You can also access it in the TMIS system using your account.\r\n<br>\r\n<br>Regards,\r\n<br>Your TMIS Admin Team\r\n<br>', 'Your transfer letter has been sent.', 'Y', '2015-01-24 00:00:00', '1', '2015-01-24 00:00:00', '1'),
(12, 'document__transfer_pca', '', 'This is the transfer PCA to be sent to _FIRST_NAME_.', '', 'Y', '2015-01-24 00:00:00', '1', '2015-01-24 00:00:00', '1'),
(13, 'send_transfer_pca', 'Your Transfer PCA', 'Hi _FIRST_NAME_,\r\n<br>\r\n<br>You have been issued a transfer PCA for your application which is attached. You can also access it in the TMIS system using your account.\r\n<br>\r\n<br>Regards,\r\n<br>Your TMIS Admin Team\r\n<br>', 'Your transfer PCA has been sent.', 'Y', '2015-01-24 00:00:00', '1', '2015-01-24 00:00:00', '1'),
(14, 'document__verification_letter', '', 'This is the verification letter to be sent to _FIRST_NAME_.', '', 'Y', '2015-01-24 00:00:00', '1', '2015-01-24 00:00:00', '1'),
(15, 'send_verification_letter', 'Your Verification Letter', 'Hi _FIRST_NAME_,\r\n<br>\r\n<br>You have been issued a verification letter for your application which is attached. You can also access it in the TMIS system using your account.\r\n<br>\r\n<br>Regards,\r\n<br>Your TMIS Admin Team\r\n<br>', 'Your verification letter has been sent.', 'Y', '2015-01-24 00:00:00', '1', '2015-01-24 00:00:00', '1'),
(16, 'document__retirement_letter', '', 'This is the retirement letter to be sent to _FIRST_NAME_.', '', 'Y', '2015-01-24 00:00:00', '1', '2015-01-24 00:00:00', '1'),
(17, 'send_retirement_letter', 'Your Retirement Letter', 'Hi _FIRST_NAME_,\r\n<br>\r\n<br>You have been issued a retirement letter for your application which is attached.\r\n<br>\r\n<br>Regards,\r\n<br>Your TMIS Admin Team\r\n<br>', 'Your retirement letter has been sent.', 'Y', '2015-01-24 00:00:00', '1', '2015-01-24 00:00:00', '1'),
(18, 'introduce_new_user', 'Your New Account Details', 'Hi _FIRST_NAME_,\r\n<br>\r\n<br>An account has been created for you at the Teacher Management Information System (TMIS). You will need the following details to login:\r\n<br>\r\nYour user name: _EMAILADDRESS_\r\n<br>Your Temporary Password: _PASSWORD_\r\n<br>\r\n<br>Use the link below to access the TMIS website:\r\n<br>_LOGIN_LINK_\r\n<br>\r\n<br>Regards,\r\n<br>Your TMIS Admin Team\r\n<br>', 'Your TMIS account has been created. Login at _LOGIN_LINK_ with username: _EMAILADDRESS_ and password: _PASSWORD_', 'Y', '2015-01-26 00:00:00', '1', '2015-01-26 00:00:00', '1'),
(19, 'notify_change_of_user_status', 'Your TMIS User Status has Been Changed', 'Hi _FIRST_NAME_,\r\n<br>\r\n<br>Your TMIS user status is now _STATUS_.\r\n<br>\r\n<br>If you believe this was made in error, please notify us immediately.\r\n<br>\r\n<br>\r\n<br>Regards,\r\n<br>Your TMIS Admin Team\r\n<br>', 'Your TMIS user status is now _STATUS_.', 'N', '2015-01-27 00:00:00', '1', '2015-01-27 00:00:00', '1'),
(20, 'reject_user_application', 'Your User Application Has Been Rejected', 'Hi _FIRST_NAME_,\r\n<br>\r\n<br>Your TMIS user application has been rejected.\r\n<br>Reasons given:\r\n<br>_REASON_\r\n<br>\r\n<br>Please re-apply with the reasons fixed at:\r\n<br>_LOGIN_LINK_\r\n<br>\r\n<br>Regards,\r\n<br>Your TMIS Admin Team\r\n<br>', 'Your TMIS user application has been rejected. Please see your email for reasons.', 'Y', '2015-01-27 00:00:00', '1', '2015-01-27 00:00:00', '1');

-- --------------------------------------------------------

--
-- Table structure for table 'query'
--

DROP TABLE IF EXISTS query;
CREATE TABLE `query` (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(300) NOT NULL,
  details text NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

--
-- Dumping data for table 'query'
--

INSERT INTO query (id, code, details) VALUES
(1, 'get_list_of_districts', 'SELECT *, name AS value, name AS display FROM district ORDER BY name'),
(2, 'get_list_of_countries', 'SELECT *, name AS value, name AS display FROM country ORDER BY display_order DESC, name ASC'),
(3, 'add_person_data', 'INSERT INTO person (first_name, last_name, gender, date_of_birth, date_added) VALUES (''_FIRST_NAME_'', ''_LAST_NAME_'', ''_GENDER_'', ''_DATE_OF_BIRTH_'', NOW())'),
(4, 'add_contact_data', 'INSERT INTO contact (contact_type, carrier_id, details, parent_id, parent_type, date_added) VALUES (''_CONTACT_TYPE_'', ''_CARRIER_ID_'', ''_DETAILS_'', ''_PARENT_ID_'', ''_PARENT_TYPE_'', NOW())'),
(5, 'add_new_address', 'INSERT INTO address (parent_id, parent_type, address_type, importance, details, county, district_id, country_id, date_added)\r\n\r\n(SELECT ''_PARENT_ID_'' AS parent_id, \r\n''_PARENT_TYPE_'' AS parent_type, \r\n''_ADDRESS_TYPE_'' AS address_type, \r\n''_IMPORTANCE_'' AS importance, \r\n''_DETAILS_'' AS details, \r\n ''_COUNTY_'' AS county,\r\nIF((SELECT id FROM district WHERE name=''_DISTRICT_'' LIMIT 1) IS NOT NULL, (SELECT id FROM district WHERE name=''_DISTRICT_'' LIMIT 1), ''_DISTRICT_'') AS district_id, \r\nIF((SELECT id FROM country WHERE name=''_COUNTRY_'' LIMIT 1) IS NOT NULL, (SELECT id FROM country WHERE name=''_COUNTRY_'' LIMIT 1), ''_COUNTRY_'') AS country_id,\r\nNOW() AS date_added\r\n)'),
(6, 'add_user_data', 'INSERT INTO user (person_id, 	login_name, login_password, permission_group_id, status, date_added) \r\n\r\n(SELECT ''_PERSON_ID_'' AS person_id, ''_LOGIN_NAME_'' AS login_name, ''_LOGIN_PASSWORD_'' AS login_password, IF(''_PERMISSION_GROUP_''<>'''', (SELECT id FROM permission_group WHERE notes=''_PERMISSION_GROUP_'' LIMIT 1), '''') AS permission_group_id, ''_STATUS_'' AS status, NOW() AS date_added)'),
(7, 'get_message_template', 'SELECT * FROM message WHERE message_type=''_MESSAGE_TYPE_'''),
(8, 'add_event_log', 'INSERT INTO log (log_code, result, details, date_added) VALUES (''_LOG_CODE_'', ''_RESULT_'', ''_DETAILS_'', NOW())'),
(9, 'update_person_profile_part', 'UPDATE person SET _QUERY_PART_ WHERE id=''_PERSON_ID_'''),
(10, 'update_person_citizenship', 'UPDATE person P LEFT JOIN country C ON (C.name=''_CITIZEN_COUNTRY_'') \r\nSET P.citizenship_id=C.id, P.citizenship_type=''_CITIZENSHIP_TYPE_'' WHERE P.id=''_PERSON_ID_'''),
(11, 'add_another_id', 'INSERT INTO other_user_id (parent_id, parent_type, id_type, id_value, date_added) VALUES (''_PARENT_ID_'', ''_PARENT_TYPE_'', ''_ID_TYPE_'', ''_ID_VALUE_'', NOW())'),
(12, 'add_academic_history', 'INSERT INTO academic_history (person_id, institution, start_date, end_date, certificate_name, certificate_number, is_highest, date_added, added_by) VALUES (''_PERSON_ID_'', ''_INSTITUTION_'', ''_START_DATE_'', ''_END_DATE_'', ''_CERTIFICATE_NAME_'', ''_CERTIFICATE_NUMBER_'', ''_IS_HIGHEST_'', NOW(), ''_ADDED_BY_'')'),
(13, 'add_subject_data', 'INSERT INTO subject (details, study_category, parent_id, parent_type) VALUES (''_DETAILS_'', ''_STUDY_CATEGORY_'', ''_PARENT_ID_'', ''_PARENT_TYPE_'')'),
(14, 'remove_academic_history', 'DELETE FROM academic_history WHERE person_id=''_PERSON_ID_'''),
(15, 'remove_subject_data', 'DELETE FROM subject WHERE parent_id=''_PARENT_ID_'' AND parent_type=''_PARENT_TYPE_'''),
(16, 'update_person_data', 'UPDATE person SET first_name=''_FIRST_NAME_'', last_name=''_LAST_NAME_'', gender=''_GENDER_'', date_of_birth=''_DATE_OF_BIRTH_'' WHERE id=''_PERSON_ID_'''),
(17, 'update_contact_data', 'UPDATE contact SET details=''_DETAILS_'', carrier_id=''_CARRIER_ID_''  WHERE contact_type=''_CONTACT_TYPE_'' AND parent_id=''_PARENT_ID_'' AND parent_type=''_PARENT_TYPE_'''),
(18, 'update_address_data', 'UPDATE address SET \r\n\r\ndetails=''_DETAILS_'', \r\n\r\naddress_type=''_ADDRESS_TYPE_'',\r\n \r\ncounty=''_COUNTY_'',\r\n \r\ndistrict_id=(SELECT IF((SELECT id FROM district WHERE name=''_DISTRICT_'' LIMIT 1) IS NOT NULL, (SELECT id FROM district WHERE name=''_DISTRICT_'' LIMIT 1), ''_DISTRICT_'') AS district_id), \r\n\r\ncountry_id=(SELECT IF((SELECT id FROM country WHERE name=''_COUNTRY_'' LIMIT 1) IS NOT NULL, (SELECT id FROM country WHERE name=''_COUNTRY_'' LIMIT 1), ''_COUNTRY_'') AS country_id)\r\n\r\nWHERE parent_id =''_PARENT_ID_'' AND parent_type = ''_PARENT_TYPE_'' AND importance = ''_IMPORTANCE_'''),
(19, 'update_another_id', 'UPDATE other_user_id SET id_value=''_ID_VALUE_'' WHERE parent_id=''_PARENT_ID_'' AND parent_type=''_PARENT_TYPE_'' AND id_type=''_ID_TYPE_'''),
(20, 'record_message_exchange', 'INSERT INTO message_exchange (message_id, send_format, details, subject, date_added, sender, recipient_id)\r\n\r\n(SELECT id AS message_id, ''_SEND_FORMAT_'' AS send_format, ''_DETAILS_'' AS details, ''_SUBJECT_'' AS subject, NOW() AS date_added, ''_SENDER_ID_'' AS sender, ''_RECIPIENT_ID_'' AS recipient_id FROM message WHERE message_type=''_CODE_'')'),
(21, 'get_user_by_name_and_pass', 'SELECT U.*, P.first_name, P.last_name, P.gender, P.date_of_birth, PG.notes AS permission_group_name,\r\n\r\n(SELECT P.code FROM permission_group G LEFT JOIN permission P ON (G.default_permission=P.id) WHERE G.id=U.permission_group_id LIMIT 1) AS default_permission_code,\r\n\r\n(SELECT details FROM contact WHERE contact_type=''email'' AND parent_id=P.id AND parent_type=''person'' AND status=''active'' LIMIT 1) AS email_address,\r\n\r\nIF((SELECT details FROM contact WHERE contact_type=''telephone'' AND parent_id=P.id AND parent_type=''person'' AND status=''active'' LIMIT 1) IS NOT NULL, (SELECT details FROM contact WHERE contact_type=''telephone'' AND parent_id=P.id AND parent_type=''person'' AND status=''active'' LIMIT 1), '''') AS telephone \r\n\r\nFROM user U LEFT JOIN person P ON (U.person_id=P.id) LEFT JOIN permission_group PG ON (U.permission_group_id=PG.id)\r\nWHERE login_name=''_LOGIN_NAME_'' AND login_password=''_LOGIN_PASSWORD_'' AND status=''active'''),
(22, 'get_user_by_email', 'SELECT U.id AS user_id, P.first_name, P.last_name \r\nFROM contact C LEFT JOIN person P ON (C.parent_id=P.id AND C.contact_type=''email'' AND C.parent_type=''person'') LEFT JOIN user U ON (U.person_id=P.id) \r\nWHERE C.details=''_EMAIL_ADDRESS_'''),
(23, 'get_group_permissions', 'SELECT P.code \r\nFROM permission_group_mapping M LEFT JOIN permission P ON (M.permission_id=P.id) \r\nWHERE M.group_id=''_GROUP_ID_'''),
(24, 'activate_teacher_applicant_user', 'UPDATE user SET status=''active'', last_updated=NOW(), permission_group_id=(SELECT id FROM permission_group WHERE name=''applicant'' LIMIT 1) WHERE person_id=''_PERSON_ID_'' AND permission_group_id='''''),
(25, 'get_user_by_id', 'SELECT *, id AS user_id FROM user WHERE id=''_USER_ID_'''),
(26, 'get_group_by_id', 'SELECT * FROM permission_group WHERE id=''_GROUP_ID_'''),
(27, 'get_group_default_permission', 'SELECT P.code, P.url AS page FROM permission_group G LEFT JOIN permission P ON (G.default_permission=P.id) WHERE G.id = ''_GROUP_ID_'' LIMIT 1'),
(28, 'get_permission_by_code', 'SELECT * FROM permission WHERE code=''_CODE_'''),
(29, 'get_permission_details', 'SELECT * FROM permission WHERE code IN (_PERMISSIONS_) AND status=''active'''),
(30, 'get_user_profile', 'SELECT U.id AS user_id, U.person_id, U.login_name, U.date_added, P.first_name, P.last_name,\r\n\r\n(SELECT notes FROM permission_group WHERE id=U.permission_group_id LIMIT 1) AS user_role, \r\n \r\n(SELECT details FROM contact WHERE parent_id=U.person_id AND parent_type=''person'' AND contact_type=''email'' AND status=''active'' LIMIT 1) AS email_address, \r\n\r\n(SELECT details FROM contact WHERE parent_id=U.person_id AND parent_type=''person'' AND contact_type=''telephone'' AND status=''active'' LIMIT 1) AS telephone \r\n\r\nFROM user U LEFT JOIN person P ON (P.id=U.person_id) \r\nWHERE U.id=''_USER_ID_'''),
(31, 'update_user_password', 'UPDATE user SET login_password=''_NEW_PASSWORD_'', previous_password=''_OLD_PASSWORD_'', last_updated=NOW(), last_updated_by=''_UPDATED_BY_'' WHERE id=''_USER_ID_'''),
(32, 'get_list_of_institutions', 'SELECT *, name AS value, name AS display FROM institution WHERE _SEARCH_QUERY_ ORDER BY name'),
(33, 'get_permission_groups', 'SELECT *, notes AS value, notes AS display FROM permission_group WHERE is_system_only IN (_SYSTEM_ONLY_) ORDER BY notes'),
(34, 'add_vacancy_data', 'INSERT INTO vacancy (school_id, start_date, end_date, topic, summary, details, role_id, date_added, added_by)\r\n\r\n(SELECT \r\n(SELECT id FROM institution WHERE name=''_INSTITUTION_'' LIMIT 1) AS school_id, \r\n\r\n''_START_DATE_'' AS start_date, ''_END_DATE_'' AS end_date, ''_TOPIC_'' AS topic, ''_SUMMARY_'' AS summary, ''_DETAILS_'', \r\n\r\n(SELECT id FROM permission_group WHERE notes=''_ROLE_'' LIMIT 1) AS role_id, \r\n\r\nNOW() AS date_added, ''_ADDED_BY_'' AS added_by\r\n)'),
(35, 'update_vacancy_data', 'UPDATE vacancy SET topic=''_TOPIC_'', summary=''_SUMMARY_'', details=''_DETAILS_'', start_date=''_START_DATE_'', end_date=''_END_DATE_'', date_added=NOW() WHERE id=''_VACANCY_ID_'''),
(36, 'get_vacancy_list_data', 'SELECT V.id, V.start_date, V.end_date, V.topic, V.summary, V.status, I.name AS institution_name, G.notes AS role_name \r\nFROM vacancy V LEFT JOIN institution I ON (V.school_id=I.id) LEFT JOIN permission_group G ON (V.role_id=G.id) \r\nWHERE _SEARCH_QUERY_ ORDER BY _ORDER_BY_ LIMIT _LIMIT_TEXT_;'),
(37, 'get_vacancy_by_id', 'SELECT V.*, I.name AS institution_name, G.notes AS role_name \r\nFROM vacancy V LEFT JOIN institution I ON (V.school_id=I.id) LEFT JOIN permission_group G ON (V.role_id=G.id) \r\nWHERE V.id=''_VACANCY_ID_'';'),
(38, 'add_approval_chain', 'INSERT INTO approval_chain (chain_type, step_number, subject_id, originator_id, approver_id, status, date_created) VALUES (''_CHAIN_TYPE_'', ''_STEP_NUMBER_'', ''_SUBJECT_ID_'', ''_ORIGINATOR_ID_'', ''_APPROVER_ID_'', ''_STATUS_'', NOW())'),
(39, 'get_approval_chain_setting', 'SELECT * FROM approval_chain_setting WHERE chain_type=''_CHAIN_TYPE_'' AND step_number=''_STEP_NUMBER_'''),
(40, 'get_approval_chain_by_id', 'SELECT C.*, \r\n(SELECT CONCAT(P1.first_name, '' '', P1.last_name) FROM user U1 LEFT JOIN person P1 ON (P1.id=U1.person_id) WHERE U1.id=C.originator_id LIMIT 1) AS originator_name, \r\n\r\nIF(C.actual_approver <> '''', (SELECT CONCAT(P2.first_name, '' '', P2.last_name) FROM user U2 LEFT JOIN person P2 ON (P2.id=U2.person_id) WHERE U2.id=C.actual_approver LIMIT 1), '''') AS approver_name \r\n\r\nFROM approval_chain C WHERE C.id=''_CHAIN_ID_'''),
(41, 'get_parties_in_chain', 'SELECT actual_approver	FROM approval_chain WHERE subject_id=''_SUBJECT_ID_'' AND status=''approved'''),
(42, 'get_originator_of_chain', 'SELECT actual_approver AS originator FROM approval_chain WHERE subject_id=''_SUBJECT_ID_'' AND step_number=''1'''),
(43, 'update_vacancy_status', 'UPDATE vacancy SET status=''_STATUS_'' WHERE id=''_VACANCY_ID_'''),
(44, 'deactivate_user_profile', 'UPDATE user SET status=''inactive'' WHERE id=''_USER_ID_'''),
(45, 'update_profile_visibility', 'UPDATE person SET is_visible=''_IS_VISIBLE_'' WHERE id=''_PERSON_ID_'''),
(46, 'activate_teacher_data', 'UPDATE user SET permission_group_id = (SELECT id FROM permission_group WHERE name=''teacher'' LIMIT 1), status=''active'', last_updated=NOW(), last_updated_by=''_UPDATED_BY_'' \r\nWHERE id IN (_ID_LIST_)'),
(47, 'get_approver_scope', 'SELECT * FROM approval_chain_scope WHERE approver IN (_GROUP_LIST_)'),
(48, 'get_users_in_group', 'SELECT U.id AS user_id \r\nFROM user U \r\nLEFT JOIN permission_group G ON (G.id=U.permission_group_id) \r\nLEFT JOIN posting PT ON (U.id=PT.postee_id AND PT.status=''confirmed'' AND (posting_end_date=''0000-00-00'' OR posting_end_date > NOW())) \r\nLEFT JOIN institution I ON (PT.institution_id=I.id)\r\nLEFT JOIN address A ON (A.parent_type=''institution'' AND A.parent_id=I.id AND A.status IN (''active'',''verified''))\r\n\r\nWHERE G.name=''_GROUP_'' _CONDITION_'),
(49, 'get_originator_scope', 'SELECT U.id AS originator_id,\r\n\r\n(SELECT GROUP_CONCAT(PT.institution_id) FROM posting PT WHERE U.id=PT.postee_id AND PT.status IN (''pending'',''confirmed'') AND (PT.posting_end_date=''0000-00-00'' OR PT.posting_end_date > NOW())) AS institutions,\r\n\r\n(SELECT IF(A.county <> '''' AND A.county IS NOT NULL, GROUP_CONCAT(A.county), IF(A.district_id <>'''', (SELECT GROUP_CONCAT(C.name) FROM county C WHERE C.district_id=A.district_id), '''')) \r\nFROM posting PT \r\nLEFT JOIN address A ON (A.parent_type=''institution'' AND A.parent_id=PT.institution_id AND A.status IN (''active'',''verified'')) WHERE U.id=PT.postee_id AND PT.status IN (''pending'',''confirmed'') AND (PT.posting_end_date=''0000-00-00'' OR PT.posting_end_date > NOW())) AS counties,\r\n\r\n(SELECT GROUP_CONCAT(A.district_id) FROM posting PT \r\nLEFT JOIN address A ON (A.parent_type=''institution'' AND A.parent_id=PT.institution_id AND A.status IN (''active'',''verified'')) WHERE U.id=PT.postee_id AND PT.status IN (''pending'',''confirmed'') AND (PT.posting_end_date=''0000-00-00'' OR PT.posting_end_date > NOW())) AS districts\r\n \r\nFROM user U \r\nWHERE U.id=''_ORIGINATOR_ID_'''),
(50, 'get_carrier_email_domain', 'SELECT IF(mms_email_domain <>'''', mms_email_domain, sms_email_domain) AS email_domain FROM carrier WHERE number_stubs LIKE ''_NUMBER_STUB_'' OR number_stubs LIKE ''_NUMBER_STUB_,%'' OR number_stubs LIKE ''%,_NUMBER_STUB_,%'' OR number_stubs LIKE ''%,_NUMBER_STUB_'''),
(51, 'get_step_chain', 'SELECT * FROM approval_chain WHERE chain_type=''_CHAIN_TYPE_'' AND step_number=''_STEP_NUMBER_'' AND subject_id=''_SUBJECT_ID_'''),
(52, 'get_user_list_data', 'SELECT U.*, P.first_name, P.last_name,\r\n\r\n(SELECT notes FROM permission_group WHERE id=U.permission_group_id LIMIT 1) AS user_role, \r\n \r\n(SELECT details FROM contact WHERE parent_id=U.person_id AND parent_type=''person'' AND contact_type=''email'' AND status=''active'' LIMIT 1) AS email_address, \r\n\r\n(SELECT details FROM contact WHERE parent_id=U.person_id AND parent_type=''person'' AND contact_type=''telephone'' AND status=''active'' LIMIT 1) AS telephone \r\n\r\nFROM user U LEFT JOIN person P ON (P.id=U.person_id) WHERE _SEARCH_QUERY_ _ORDER_BY_ LIMIT _LIMIT_TEXT_'),
(53, 'update_user_status', 'UPDATE user SET status=''_STATUS_'' WHERE id=''_USER_ID_'''),
(54, 'delete_user_data', 'DELETE U, P, A \r\nFROM user U \r\nJOIN person P ON (U.person_id=P.id) \r\nJOIN address A ON (A.parent_type=''person'' AND A.parent_id=P.id) \r\nWHERE U.id=''_USER_ID_'''),
(55, 'update_user_permission_group', 'UPDATE user SET permission_group_id=(SELECT id  FROM permission_group WHERE name=''_PERMISSION_GROUP_'' LIMIT 1), last_updated_by=''_UPDATED_BY_'', last_updated=NOW() WHERE id=''_USER_ID_''');






