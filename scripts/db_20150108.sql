SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

CREATE DATABASE IF NOT EXISTS tmis DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE tmis;

CREATE TABLE IF NOT EXISTS academic_history (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  person_id varchar(100) NOT NULL,
  institution_id varchar(100) NOT NULL,
  start_date text NOT NULL,
  end_date date NOT NULL,
  qualification_id varchar(100) NOT NULL,
  notes text NOT NULL,
  date_added datetime NOT NULL,
  added_by varchar(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS address (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  parent_id varchar(100) NOT NULL,
  parent_type varchar(100) NOT NULL,
  address_type enum('physical','postal') NOT NULL DEFAULT 'physical',
  is_primary enum('Y','N') NOT NULL DEFAULT 'N',
  details varchar(500) NOT NULL,
  sub_county_id varchar(100) NOT NULL,
  region_id varchar(100) NOT NULL,
  district_id varchar(100) NOT NULL,
  country_id varchar(100) NOT NULL,
  zipcode varchar(10) NOT NULL DEFAULT '256',
  direction_notes text NOT NULL,
  `status` enum('pending','active','verified','inactive') NOT NULL DEFAULT 'pending',
  date_added datetime NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS application (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  person_id varchar(100) NOT NULL,
  vacancy_id varchar(100) NOT NULL,
  `status` enum('saved','submitted','shortlisted','awarded','rejected','archived') NOT NULL DEFAULT 'saved',
  user_id varchar(100) NOT NULL,
  date_added datetime NOT NULL,
  added_by varchar(100) NOT NULL,
  last_updated datetime NOT NULL,
  last_updated_by varchar(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS carrier (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(300) NOT NULL,
  displayed_name varchar(300) NOT NULL,
  country_code varchar(10) NOT NULL,
  number_code varchar(10) NOT NULL,
  sms_email_domain varchar(300) NOT NULL,
  mms_email_domain varchar(300) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS contact (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  contact_type varchar(100) NOT NULL,
  carrier_id varchar(100) NOT NULL,
  details varchar(300) NOT NULL,
  parent_id varchar(100) NOT NULL,
  parent_type varchar(100) NOT NULL,
  date_added datetime NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS document (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  new_url varchar(300) NOT NULL,
  original_url varchar(500) NOT NULL,
  description varchar(300) NOT NULL,
  parent_id varchar(100) NOT NULL,
  parent_type varchar(200) NOT NULL,
  date_added datetime NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS document_owner (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  parent_id varchar(100) NOT NULL,
  parent_type varchar(200) NOT NULL,
  document_id varchar(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS grade (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(100) NOT NULL,
  `name` varchar(300) NOT NULL,
  notes text NOT NULL,
  date_added datetime NOT NULL,
  added_by varchar(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS group_permission_mapping (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  group_id varchar(100) NOT NULL,
  permission_id varchar(100) NOT NULL,
  date_added datetime NOT NULL,
  added_by varchar(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS institution (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(500) NOT NULL,
  start_date date NOT NULL,
  end_date date NOT NULL,
  `status` enum('verified','pending','rejected') NOT NULL DEFAULT 'pending',
  date_added datetime NOT NULL,
  added_by varchar(100) NOT NULL,
  last_updated datetime NOT NULL,
  last_updated_by varchar(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS interview (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  application_id varchar(100) NOT NULL,
  interviewer_id varchar(100) NOT NULL,
  planned_date datetime NOT NULL,
  interview_date datetime NOT NULL,
  interview_duration int(11) NOT NULL,
  notes text NOT NULL,
  result enum('pending','failed','inconclusive','passed') NOT NULL DEFAULT 'pending',
  date_added datetime NOT NULL,
  added_by varchar(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `leave` (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  reason varchar(300) NOT NULL,
  notes text NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS log (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  log_code varchar(200) NOT NULL,
  result enum('success','fail') NOT NULL DEFAULT 'success',
  details text NOT NULL,
  date_added datetime NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS message (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  message_type varchar(300) NOT NULL,
  details text NOT NULL,
  date_added datetime NOT NULL,
  added_by varchar(100) NOT NULL,
  last_updated datetime NOT NULL,
  last_updated_by varchar(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS message_exchange (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  message_id varchar(100) NOT NULL,
  send_format varchar(100) NOT NULL,
  details text NOT NULL,
  category varchar(300) NOT NULL,
  date_added datetime NOT NULL,
  sender_id varchar(100) NOT NULL,
  recipient_id varchar(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS message_status (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  message_exchange_id varchar(100) NOT NULL,
  `status` enum('received','read','replied','archived') NOT NULL DEFAULT 'received',
  date_added datetime NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS other_user_id (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  parent_id varchar(100) NOT NULL,
  parent_type varchar(100) NOT NULL,
  id_type varchar(100) NOT NULL,
  id_value varchar(300) NOT NULL,
  `status` enum('active','pending','inactive') NOT NULL DEFAULT 'pending',
  date_added datetime NOT NULL,
  added_by varchar(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS permission (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  details varchar(300) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS permission_group (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(300) NOT NULL,
  notes text NOT NULL,
  date_added datetime NOT NULL,
  added_by varchar(100) NOT NULL,
  last_updated datetime NOT NULL,
  last_updated_by varchar(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS person (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  prefix varchar(10) NOT NULL,
  first_name varchar(300) NOT NULL,
  middle_name varchar(100) NOT NULL,
  last_name varchar(300) NOT NULL,
  suffix varchar(10) NOT NULL,
  gender enum('male','female','unknown') NOT NULL DEFAULT 'unknown',
  date_of_birth date NOT NULL,
  date_added datetime NOT NULL,
  added_by varchar(100) NOT NULL,
  last_updated datetime NOT NULL,
  last_updated_by varchar(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS posting (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  postee_id varchar(100) NOT NULL,
  posting_start_date date NOT NULL,
  posting_end_date date NOT NULL,
  notes text NOT NULL,
  role_id varchar(100) NOT NULL,
  `status` enum('reversed','pending','confirmed') NOT NULL DEFAULT 'pending',
  date_added datetime NOT NULL,
  added_by varchar(100) NOT NULL,
  last_updated datetime NOT NULL,
  last_updated_by varchar(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS qualification (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  parent_id varchar(100) NOT NULL,
  parent_type varchar(100) NOT NULL,
  qualification_type varchar(100) NOT NULL,
  qualification_number varchar(200) NOT NULL,
  details varchar(500) NOT NULL,
  issued_by_id varchar(100) NOT NULL,
  document_id varchar(100) NOT NULL,
  grade_id varchar(100) NOT NULL,
  `status` enum('reversed','awarded','inprogress') NOT NULL DEFAULT 'awarded',
  date_achieved date NOT NULL,
  date_added datetime NOT NULL,
  added_by varchar(100) NOT NULL,
  last_updated datetime NOT NULL,
  last_updated_by varchar(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `query` (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(300) NOT NULL,
  details text NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS recommendation (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  change_application_id varchar(100) NOT NULL,
  recommended_by varchar(100) NOT NULL,
  notes text NOT NULL,
  date_added datetime NOT NULL,
  added_by varchar(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS reference (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  person_id varchar(100) NOT NULL,
  `status` enum('verified','pending','rejected') NOT NULL DEFAULT 'pending',
  date_added datetime NOT NULL,
  added_by varchar(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS role (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(300) NOT NULL,
  `code` varchar(100) NOT NULL,
  notes text NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  date_added datetime NOT NULL,
  added_by varchar(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS saved_vacancies (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  user_id varchar(100) NOT NULL,
  vacancy_id varchar(100) NOT NULL,
  date_added datetime NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS school (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(500) NOT NULL,
  date_registered date NOT NULL,
  school_type varchar(100) NOT NULL,
  `status` enum('active','pending','inactive') NOT NULL DEFAULT 'pending',
  date_added datetime NOT NULL,
  added_by varchar(100) NOT NULL,
  last_updated datetime NOT NULL,
  last_updated_by varchar(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS status_change_application (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  change_type varchar(200) NOT NULL,
  change_id varchar(100) NOT NULL,
  old_school_id varchar(100) NOT NULL,
  new_school_id varchar(100) NOT NULL,
  applicant_id varchar(100) NOT NULL,
  old_applicant_role_id varchar(100) NOT NULL,
  new_applicant_role_id varchar(100) NOT NULL,
  start_date date NOT NULL,
  end_date date NOT NULL,
  notes text NOT NULL,
  date_added datetime NOT NULL,
  added_by varchar(100) NOT NULL,
  last_updated datetime NOT NULL,
  last_updated_by varchar(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `subject` (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  details varchar(500) NOT NULL,
  parent_id varchar(100) NOT NULL,
  parent_type varchar(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `user` (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  person_id varchar(100) NOT NULL,
  permission_group_id varchar(100) NOT NULL,
  login_name varchar(100) NOT NULL,
  login_password varchar(300) NOT NULL,
  previous_password varchar(300) NOT NULL,
  date_added datetime NOT NULL,
  added_by varchar(100) NOT NULL,
  `status` enum('pending','active','inactive','blocked') NOT NULL DEFAULT 'pending',
  last_updated datetime NOT NULL,
  last_updated_by varchar(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS user_status_tracking (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  change_application_id varchar(100) NOT NULL,
  `status` enum('pending','verified','approved','rejected','ended') NOT NULL DEFAULT 'pending',
  date_added datetime NOT NULL,
  added_by varchar(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS vacancy (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  school_id varchar(100) NOT NULL,
  start_date date NOT NULL,
  end_date date NOT NULL,
  topic varchar(200) NOT NULL,
  summary varchar(500) NOT NULL,
  details text NOT NULL,
  `status` enum('published','saved','archived') NOT NULL DEFAULT 'saved',
  role_id varchar(100) NOT NULL,
  date_added datetime NOT NULL,
  added_by varchar(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
