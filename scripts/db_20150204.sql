
ALTER TABLE `vacancy` ADD `last_updated` DATETIME NOT NULL, 
ADD `last_updated_by` VARCHAR( 100 ) NOT NULL;

ALTER TABLE `application` DROP `person_id` ;
ALTER TABLE `application` ADD CONSTRAINT vacancy_id UNIQUE (`vacancy_id` ,`user_id`);

ALTER TABLE `saved_vacancies` ADD CONSTRAINT user_id UNIQUE (`user_id` ,`vacancy_id`);

ALTER TABLE `recommendation` ADD `application_type` VARCHAR( 100 ) NOT NULL AFTER `change_application_id` ;
ALTER TABLE `recommendation` ADD UNIQUE (`change_application_id` ,`application_type` ,`recommended_by`);

ALTER TABLE `interview` CHANGE `result` `result` ENUM( 'pending', 'failed', 'inconclusive', 'passed', 'awarded' ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'pending';
ALTER TABLE `interview` ADD `last_updated` DATETIME NOT NULL ,
ADD `last_updated_by` VARCHAR( 100 ) NOT NULL ;


CREATE TABLE note (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  parent_id varchar(100) NOT NULL,
  parent_type varchar(100) NOT NULL,
  details text NOT NULL,
  date_added datetime NOT NULL,
  added_by varchar(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



CREATE TABLE shortlist (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  shortlist_name varchar(300) NOT NULL,
  vacancy_id varchar(100) NOT NULL,
  applicant_id varchar(100) NOT NULL,
  date_added datetime NOT NULL,
  added_by varchar(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



ALTER TABLE `shortlist` ADD UNIQUE (`shortlist_name` ,`vacancy_id` ,`applicant_id`);

ALTER TABLE `posting` CHANGE `status` `status` ENUM( 'saved', 'reversed', 'pending', 'confirmed', 'verified' ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'pending';
ALTER TABLE `posting` ADD `applied_for_confirmation` ENUM( 'Y', 'N' ) NOT NULL DEFAULT 'N' AFTER `status` ;
ALTER TABLE `posting` ADD `final_interview_id` VARCHAR( 100 ) NOT NULL AFTER `status` ;



ALTER TABLE `duty` ADD `description` TEXT NOT NULL ;


--
-- Table structure for table 'transfer'
--

CREATE TABLE transfer (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  teacher_id varchar(100) NOT NULL,
  new_school_id varchar(100) NOT NULL,
  reason text NOT NULL,
  proposed_date date NOT NULL,
  actual_date date NOT NULL,
  `status` enum('pending','institutionapproved','countyapproved','pcaissued','confirmed','archived') NOT NULL DEFAULT 'pending',
  date_added datetime NOT NULL,
  added_by varchar(100) NOT NULL,
  last_updated datetime NOT NULL,
  last_updated_by varchar(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;





DROP TABLE IF EXISTS `leave`;
CREATE TABLE `leave` (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  teacher_id varchar(100) NOT NULL,
  proposed_start_date date NOT NULL,
  actual_start_date date NOT NULL,
  proposed_end_date date NOT NULL,
  actual_end_date date NOT NULL,
  reason varchar(300) NOT NULL,
  notes text NOT NULL,
  `status` enum('pending','districtapproved','confirmed','rejected','archived') NOT NULL DEFAULT 'pending',
  date_added datetime NOT NULL,
  added_by varchar(100) NOT NULL,
  last_updated datetime NOT NULL,
  last_updated_by varchar(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE `address` ADD UNIQUE (`parent_id` ,`parent_type` ,`importance`);

ALTER TABLE `other_user_id` ADD UNIQUE (`parent_id` ,`parent_type` ,`id_type`);



INSERT INTO `approval_chain_scope` (`id`, `approver`, `scope`) VALUES (NULL, 'esc', 'country');


DROP TABLE IF EXISTS approval_chain_setting;
CREATE TABLE approval_chain_setting (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  chain_type varchar(100) NOT NULL,
  step_number int(11) NOT NULL,
  max_steps int(11) NOT NULL,
  originators varchar(300) NOT NULL,
  approvers varchar(300) NOT NULL,
  step_actions varchar(500) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

INSERT INTO approval_chain_setting (id, chain_type, step_number, max_steps, originators, approvers, step_actions) VALUES
(1, 'vacancy', 1, 3, '', 'admin,moes,deo', 'notify_next_chain_party'),
(2, 'vacancy', 2, 3, 'admin,moes,deo', 'mops', 'notify_previous_and_next_chain_parties'),
(3, 'vacancy', 3, 3, 'mops', 'admin,esc', 'publish_job_notice,notify_previous_chain_parties'),
(4, 'confirmation', 1, 3, '', 'teacher', 'notify_next_chain_party'),
(5, 'confirmation', 2, 3, 'teacher', 'moes,cao', 'issue_confirmation_letter,notify_previous_and_next_chain_parties'),
(6, 'confirmation', 3, 3, 'moes,cao', 'esc,dsc', 'notify_previous_chain_parties'),
(7, 'registration', 1, 3, '', 'admin,data,hr,teacher', 'notify_next_chain_party'),
(8, 'registration', 2, 3, 'admin,data,hr,teacher', 'hr', 'issue_file_number,notify_previous_and_next_chain_parties'),
(9, 'registration', 3, 3, 'hr', 'tiet', 'issue_registration_certificate,notify_previous_chain_parties'),
(10, 'transfer', 1, 5, '', 'teacher,manager', 'notify_next_chain_party'),
(11, 'transfer', 2, 5, 'teacher,manager', 'manager', 'notify_previous_and_next_chain_parties'),
(12, 'transfer', 3, 5, 'manager', 'moes,cao', 'issue_transfer_letter,change_teacher_posting,notify_previous_and_next_chain_parties'),
(13, 'transfer', 4, 5, 'moes,cao', 'manager', 'submit_transfer_pca,notify_previous_and_next_chain_parties'),
(14, 'transfer', 5, 5, 'manager', 'psc', 'confirm_transfer,notify_previous_chain_parties'),
(15, 'leave', 1, 4, '', 'teacher,manager', 'notify_next_chain_party'),
(16, 'leave', 2, 4, 'teacher,manager', 'moes,cao', 'notify_previous_and_next_chain_parties'),
(17, 'leave', 3, 4, 'moes,cao', 'esc,dsc', 'notify_previous_and_next_chain_parties'),
(18, 'leave', 4, 4, 'esc,dsc', 'moes,cao', 'send_verification_letter,notify_previous_chain_parties'),
(19, 'retirement', 1, 2, '', 'teacher,manager', 'notify_next_chain_party'),
(20, 'retirement', 2, 2, 'teacher,manager', 'manager', 'confirm_retirement,notify_previous_chain_parties'),
(21, 'secrecy', 1, 2, '', 'teacher', 'notify_next_chain_party'),
(22, 'secrecy', 2, 2, 'teacher', 'admin,esc,dsc', 'apply_data_secrecy,notify_previous_chain_parties'),
(23, 'data', 1, 2, '', 'admin,data', 'notify_next_chain_party'),
(24, 'data', 2, 2, 'admin,data', 'admin', 'activate_data_records,notify_previous_chain_parties'),
(25, 'recommendation', 1, 2, '', 'manager', 'notify_next_chain_party'),
(26, 'recommendation', 2, 2, 'manager', 'esc,dsc', 'notify_previous_chain_parties'),
(27, 'census', 1, 2, '', 'manager', 'notify_next_chain_party'),
(28, 'census', 2, 2, 'manager', 'moes,cao', 'notify_previous_chain_parties');



ALTER TABLE `approval_chain` ADD `max_steps` INT NOT NULL AFTER `step_number` ;

UPDATE `approval_chain` A SET A.max_steps = (SELECT S.max_steps FROM approval_chain_setting S WHERE S.chain_type=A.chain_type LIMIT 1)


ALTER TABLE `person` ADD `photo` VARCHAR( 300 ) NOT NULL AFTER `signature` ;


-- Updated ONLINE up to here

DROP TABLE IF EXISTS document;
CREATE TABLE document (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  url varchar(300) NOT NULL,
  document_type varchar(200) NOT NULL,
  tracking_number varchar(200) NOT NULL,
  description varchar(300) NOT NULL,
  parent_id varchar(100) NOT NULL,
  parent_type varchar(200) NOT NULL,
  date_added datetime NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



INSERT INTO `query` (`id` ,`code` ,`details`)
VALUES (NULL , 'validate_system_document', 'SELECT * FROM document WHERE document_type=''_DOCUMENT_TYPE_'' AND tracking_number=''_TRACKING_NUMBER_''');


INSERT INTO `query` (`id`, `code`, `details`) VALUES (NULL, 'add_system_document', 'INSERT INTO document (url, document_type, tracking_number, description, parent_id, parent_type, date_added) VALUES 
(''_URL_'', ''_DOCUMENT_TYPE_'', ''_TRACKING_NUMBER_'', ''_DESCRIPTION_'', ''_PARENT_ID_'', ''_PARENT_TYPE_'', NOW())');








-- remember to include the following tables: permission, permission_group_mapping, query, message

