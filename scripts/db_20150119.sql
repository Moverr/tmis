
ALTER TABLE `permission` ADD `code` VARCHAR( 200 ) NOT NULL AFTER `id` ;
ALTER TABLE `permission` ADD UNIQUE (`code`);
ALTER TABLE `permission` ADD `category` VARCHAR( 200 ) NOT NULL AFTER `details` ,
ADD `url` VARCHAR( 300 ) NOT NULL AFTER `category` ;


ALTER TABLE `permission_group` ADD `is_removable` ENUM( 'Y', 'N' ) NOT NULL DEFAULT 'Y' AFTER `notes` ;


ALTER TABLE `group_permission_mapping` ADD `is_default` ENUM( 'Y', 'N' ) NOT NULL DEFAULT 'N' AFTER `permission_id` ;
RENAME TABLE `group_permission_mapping` TO `permission_group_mapping`;


DROP TABLE IF EXISTS message;
CREATE TABLE message (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  message_type varchar(300) NOT NULL,
  `subject` varchar(300) NOT NULL,
  details text NOT NULL,
  copy_admin enum('Y','N') NOT NULL DEFAULT 'Y',
  date_added datetime NOT NULL,
  added_by varchar(100) NOT NULL,
  last_updated datetime NOT NULL,
  last_updated_by varchar(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

INSERT INTO message (id, message_type, subject, details, copy_admin, date_added, added_by, last_updated, last_updated_by) VALUES
(1, 'new_teacher_first_step', 'Your Verification Code for TMIS', 'Hi _FIRST_NAME_,\r\n<br>\r\n<br>A profile has been created for you at the Teacher Management Information System (TMIS). You will need this code to confirm your account:\r\n<br>_VERIFICATION_CODE_\r\n<br>\r\n<br>In case you closed the TMIS website, go to the link below to continue with your registration:\r\n<br>_LOGIN_LINK_\r\n<br>You will need the following details to login:\r\n<br>\r\nYour user name: _EMAILADDRESS_\r\n<br>Your Temporary Password: _PASSWORD_\r\n<br>\r\n<br>Regards,\r\n<br>Your TMIS Admin Team\r\n<br>', 'Y', '2015-01-14 00:00:00', '1', '0000-00-00 00:00:00', ''),
(2, 'teacher_application_submitted', 'Your TMIS Application Has Been Submitted', 'Hi _FIRST_NAME_,\r\n<br>\r\n<br>Your TMIS application has been submitted for review. You will be notified by email when it is approved for you to begin enjoying the benefits of a TMIS registered teacher.\r\n<br>\r\n<br>In case you closed the TMIS website, go to the link below to learn more about TMIS:\r\n<br>_LOGIN_LINK_\r\n<br>\r\n<br>Regards,\r\n<br>Your TMIS Admin Team\r\n<br>', 'Y', '2015-01-16 00:00:00', '1', '2015-01-16 00:00:00', '1');




DROP TABLE IF EXISTS query;
CREATE TABLE `query` (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(300) NOT NULL,
  details text NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

INSERT INTO query (id, code, details) VALUES
(1, 'get_list_of_districts', 'SELECT *, name AS value, name AS display FROM district ORDER BY name'),
(2, 'get_list_of_countries', 'SELECT *, name AS value, name AS display FROM country ORDER BY display_order DESC, name ASC'),
(3, 'add_person_data', 'INSERT INTO person (first_name, last_name, gender, date_of_birth, date_added) VALUES (''_FIRST_NAME_'', ''_LAST_NAME_'', ''_GENDER_'', ''_DATE_OF_BIRTH_'', NOW())'),
(4, 'add_contact_data', 'INSERT INTO contact (contact_type, carrier_id, details, parent_id, parent_type, date_added) VALUES (''_CONTACT_TYPE_'', ''_CARRIER_ID_'', ''_DETAILS_'', ''_PARENT_ID_'', ''_PARENT_TYPE_'', NOW())'),
(5, 'add_new_address', 'INSERT INTO address (parent_id, parent_type, address_type, importance, details, county, district_id, country_id, date_added)\r\n\r\n(SELECT ''_PARENT_ID_'' AS parent_id, \r\n''_PARENT_TYPE_'' AS parent_type, \r\n''_ADDRESS_TYPE_'' AS address_type, \r\n''_IMPORTANCE_'' AS importance, \r\n''_DETAILS_'' AS details, \r\n ''_COUNTY_'' AS county,\r\nIF((SELECT id FROM district WHERE name=''_DISTRICT_'' LIMIT 1) IS NOT NULL, (SELECT id FROM district WHERE name=''_DISTRICT_'' LIMIT 1), ''_DISTRICT_'') AS district_id, \r\nIF((SELECT id FROM country WHERE name=''_COUNTRY_'' LIMIT 1) IS NOT NULL, (SELECT id FROM country WHERE name=''_COUNTRY_'' LIMIT 1), ''_COUNTRY_'') AS country_id,\r\nNOW() AS date_added\r\n)'),
(6, 'add_user_data', 'INSERT INTO user (person_id, 	login_name, login_password, date_added) VALUES (''_PERSON_ID_'', ''_LOGIN_NAME_'', ''_LOGIN_PASSWORD_'', NOW())'),
(7, 'get_message_template', 'SELECT * FROM message WHERE message_type=''_MESSAGE_TYPE_'''),
(8, 'add_event_log', 'INSERT INTO log (log_code, result, details, date_added) VALUES (''_LOG_CODE_'', ''_RESULT_'', ''_DETAILS_'', NOW())'),
(9, 'update_person_profile_part', 'UPDATE person SET _QUERY_PART_ WHERE person_id=''_PERSON_ID_'''),
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
(20, 'record_message_exchange', 'INSERT INTO message_exchange (message_id, send_format, details, category, date_added, sender_id, recipient_id)\r\n\r\n(SELECT id AS message_id, ''_SEND_FORMAT_'' AS send_format, ''_DETAILS_'' AS details, ''_CATEGORY_'' AS category, NOW() AS date_added, ''_SENDER_ID_'' AS sender_id, ''_RECIPIENT_ID_'' AS recipient_id FROM message WHERE message_type=''_CODE_'')'),
(21, 'get_user_by_name_and_pass', 'SELECT U.*, P.first_name, P.last_name, P.gender, P.date_of_birth,\r\n\r\n(SELECT details FROM contact WHERE contact_type=''email'' AND parent_id=P.id AND parent_type=''person'' AND status=''active'' LIMIT 1) AS email_address,\r\n\r\nIF((SELECT details FROM contact WHERE contact_type=''telephone'' AND parent_id=P.id AND parent_type=''person'' AND status=''active'' LIMIT 1) IS NOT NULL, (SELECT details FROM contact WHERE contact_type=''telephone'' AND parent_id=P.id AND parent_type=''person'' AND status=''active'' LIMIT 1), '''') AS telephone \r\n\r\nFROM user U LEFT JOIN person P ON (U.person_id=P.id) \r\nWHERE login_name=''_LOGIN_NAME_'' AND login_password=''_LOGIN_PASSWORD_'' AND status=''active'''),
(22, 'get_user_by_email', 'SELECT U.id AS user_id, P.first_name, P.last_name \r\nFROM contact C LEFT JOIN person P ON (C.parent_id=P.id AND C.contact_type=''email'' AND C.parent_type=''person'') LEFT JOIN user U ON (U.person_id=P.id) \r\nWHERE C.details=''_EMAIL_ADDRESS_'''),
(23, 'get_group_permissions', 'SELECT P.code \r\nFROM permission_group_mapping M LEFT JOIN permissions P ON (M.permission_id=P.id) \r\nWHERE M.group_id=''_GROUP_ID_'''),
(24, 'activate_teacher_user', 'UPDATE user SET status=''active'', last_updated=NOW(), permission_group_id=(SELECT id FROM permission_group WHERE name=''applicant'' LIMIT 1) WHERE person_id=''_PERSON_ID_'' AND permission_group_id=''''');














