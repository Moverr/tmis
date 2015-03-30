
CREATE TABLE IF NOT EXISTS census (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  teacher_id varchar(100) NOT NULL,
  start_date date NOT NULL,
  end_date date NOT NULL,
  weekly_workload_average varchar(100) NOT NULL,
  status enum('pending','active','inactive') NOT NULL DEFAULT 'pending',
  added_by varchar(100) NOT NULL,
  date_added datetime NOT NULL,
  last_updated_by varchar(100) NOT NULL,
  last_updated datetime NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS census_responsibility (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  census_id varchar(100) NOT NULL,
  responsibility_id varchar(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS census_training (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  census_id varchar(100) NOT NULL,
  training_id varchar(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS responsibility (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  code varchar(100) NOT NULL,
  notes varchar(500) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS training (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  code varchar(100) NOT NULL,
  notes varchar(500) NOT NULL,
  type enum('physical','educational') NOT NULL DEFAULT 'educational',
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;



ALTER TABLE contact ADD is_primary ENUM( 'Y', 'N' ) NOT NULL DEFAULT 'Y' AFTER date_added ;
ALTER TABLE address CHANGE is_primary is_primary ENUM( 'Y', 'N' ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'Y';



ALTER TABLE message_exchange ADD attachment VARCHAR( 500 ) NOT NULL AFTER subject ;
ALTER TABLE message_status ADD UNIQUE INDEX unique_status (message_exchange_id, user_id, status);



ALTER TABLE user ADD teacher_status ENUM( 'unknown', 'pending', 'completed', 'approved', 'active', 'archived' ) NOT NULL DEFAULT 'unknown' AFTER status ;


ALTER TABLE person ADD marital_status ENUM( 'married', 'single', 'unknown' ) NOT NULL DEFAULT 'unknown' AFTER gender ;

ALTER TABLE academic_history ADD institution_type VARCHAR( 100 ) NOT NULL AFTER institution ;

ALTER TABLE grade CHANGE notes number VARCHAR( 10 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ;

ALTER TABLE `person` ADD `file_number` VARCHAR( 200 ) NOT NULL AFTER `citizenship_type` ;










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
(4, 'notify_previous_chain_parties', 'A _ITEM_TYPE_ has been _VERIFICATION_RESULT_ by _APPROVER_NAME_', 'Hi _FIRST_NAME_,\r\n<br>\r\n<br>The _ITEM_TYPE_ you acted on at TMIS has been _VERIFICATION_RESULT_ by _APPROVER_NAME_. This action was taken on _ACTION_DATE_. \r\n<br>\r\n<br>Regards,\r\n<br>Your TMIS Admin Team\r\n<br>', 'The _ITEM_TYPE_ you acted on at TMIS has been _VERIFICATION_RESULT_ by _APPROVER_NAME_.', 'Y', '2015-01-23 00:00:00', '1', '2015-01-23 00:00:00', '1'),
(5, 'document__confirmation_letter', '', 'This is the confirmation letter to be sent to _FIRST_NAME_.', '', 'Y', '2015-01-24 00:00:00', '1', '2015-01-24 00:00:00', '1'),
(6, 'issue_file_number', 'You Have Been Issued a Teacher File Number', 'Hi _FIRST_NAME_,\r\n<br>\r\n<br>You have been issued a file number: _FILE_NUMBER_.\r\n<br>\r\n<br>Use it in any of your correspondence with the ministry to allow us to process your case faster.\r\n<br>\r\n<br>Regards,\r\n<br>Your TMIS Admin Team\r\n<br>\r\n', 'You have been issued a file number: _FILE_NUMBER_. Use it in any of your correspondence with the Ministry of Education.', 'Y', '2015-01-24 00:00:00', '1', '2015-01-24 00:00:00', '1'),
(7, 'send_confirmation_letter', 'Your Confirmation Letter', 'Hi _FIRST_NAME_,\r\n<br>\r\n<br>You have been issued a confirmation letter for your application which is attached. You can also access it in the TMIS system using your account.\r\n<br>\r\n<br>Regards,\r\n<br>Your TMIS Admin Team\r\n<br>', 'Your confirmation letter has been sent.', 'Y', '2015-01-24 00:00:00', '1', '2015-01-24 00:00:00', '1'),
(8, 'document__registration_certificate', '', '<table width=''100%'' cellpadding=''0'' cellspacing=''0''>\r\n<tr><td valign=''top'' align=''left''><img src=''_ASSET_FOLDER_images/top_left_corner.png'' border=''0''  height=''50''/></td><td><table width=''100%'' cellpadding=''5'' cellspacing=''0''>\r\n  <tr>\r\n    <td style=''padding-top: 0px; padding-bottom: 0px; text-align:center; line-height:160%;''><img src=''_ASSET_FOLDER_images/coat_of_arms.png'' border=''0'' height=''100''/> <br />\r\n      <span  style=''font-family:&quot;Times New Roman&quot;, Times, serif; font-size: 20px; text-align:center;''>REPUBLIC OF UGANDA</span><br>\r\n<span style=''font-family:&quot;Times New Roman&quot;, Times, serif; font-size: 20px; text-align:center; color:#999; '' nowrap>MINISTRY OF EDUCATION AND SPORTS</span></td>\r\n  </tr>\r\n</table></td><td valign=''top'' align=''right''><img src=''_ASSET_FOLDER_images/top_right_corner.png'' border=''0'' height=''50'' /></td></tr>\r\n<tr><td colspan=''3'' align=''center''><table width=''90%'' cellpadding=''5'' cellspacing=''0'' align=''center''>\r\n\r\n  <tr>\r\n    <td style=''text-align:center;padding-bottom:0px;''><img src=''_ASSET_FOLDER_images/certificate_heading.png'' height=''70''/><br>\r\n	<span style=''font-family:&quot;Times New Roman&quot;, Times, serif; font-size: 20px; text-align:center; padding-top:0px;padding-bottom:50px;''>(Issued under Sections 11, 12 and 13 of the Education Act, 2008)</span></td>\r\n  </tr>\r\n  <tr>\r\n    <td style=''font-family:&quot;Times New Roman&quot;, Times, serif; height:75px; padding-bottom:10px; font-size: 40px; text-align:center; vertical-align:bottom;''>This is to certify that</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=''font-family:Arial, Helvetica, sans-serif; padding-bottom:10px; font-size: 30px; text-align:center; border-bottom: solid 2px #333;''>_TEACHER_NAME_</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=''font-family:&quot;Times New Roman&quot;, Times, serif; font-size: 18px; text-align:center; padding-bottom:0px; line-height:160%;''>Having completed a teacher training course approved by the Ministry, has been registered<br />\r\n       under <b>_TEACHER_GRADE_</b> with effect from _EFFECTIVE_DATE_ as number <b>_CERTIFICATE_NUMBER_</b>.</td>\r\n  </tr>\r\n  <tr>\r\n    <td><table width=''100%'' cellpadding=''5'' cellspacing=''0''>\r\n      <tr>\r\n        <td style=''font-family:&quot;Times New Roman&quot;, Times, serif; font-size: 20px; text-align:left; color:#999; ''>_DATE_TODAY_</td>\r\n        <td style=''width:400px;''>For\r\n          <div style=''border-bottom: 1px solid #000; padding-bottom: 2px; width: 95%; margin-left:10px; display:inline-block;''><img src=''_ASSET_FOLDER_uploads/images/_SIGNATURE_URL_'' border=''0'' style=''height:80px;'' /></div>\r\n          <br />\r\n          <span style=''&quot;Times New Roman&quot;, Times, serif; font-size: 12px;''>DIRECTOR FOR HIGHER TECHNICAL, VOCATIONAL AND EDUCATIONAL TRAINING.</span></td>\r\n      </tr>\r\n    </table></td>\r\n  </tr>\r\n</table></td>\r\n  </tr>\r\n<tr><td valign=''bottom'' align=''left'' style=''padding-top:0px;''><img src=''_ASSET_FOLDER_images/bottom_left_corner.png'' border=''0'' height=''50'' /></td>\r\n  <td style=''text-align:center;padding-top:0px; padding-bottom:0px;''>&nbsp;</td>\r\n  <td valign=''bottom'' align=''right'' style=''padding-top:0px;''><img src=''_ASSET_FOLDER_images/bottom_right_corner.png'' border=''0'' height=''50'' /></td></tr>\r\n</table>', '', 'Y', '2015-01-24 00:00:00', '1', '2015-01-24 00:00:00', '1'),
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
(20, 'reject_user_application', 'Your User Application Has Been Rejected', 'Hi _FIRST_NAME_,\r\n<br>\r\n<br>Your TMIS user application has been rejected.\r\n<br>Reasons given:\r\n<br>_REASON_\r\n<br>\r\n<br>Please re-apply with the reasons fixed at:\r\n<br>_LOGIN_LINK_\r\n<br>\r\n<br>Regards,\r\n<br>Your TMIS Admin Team\r\n<br>', 'Your TMIS user application has been rejected. Please see your email for reasons.', 'Y', '2015-01-27 00:00:00', '1', '2015-01-27 00:00:00', '1'),
(21, 'reject_permission_group', 'Your Permission Group Addition Has Been Rejected', 'Hi _FIRST_NAME_,\r\n<br>\r\n<br>Your TMIS permission group addition has been rejected.\r\n<br>Reasons given:\r\n<br>_REASON_\r\n<br>\r\n<br>Please re-add with the reasons fixed at:\r\n<br>_LOGIN_LINK_\r\n<br>\r\n<br>Regards,\r\n<br>Your TMIS Admin Team\r\n<br>', 'Your TMIS permission group addition has been rejected. Please see your email for reasons.', 'Y', '2015-01-27 00:00:00', '1', '2015-01-27 00:00:00', '1'),
(22, 'user_defined_message', '_USER_DEFINED_', '_USER_DEFINED_', '_USER_DEFINED_', 'Y', '2015-01-30 00:00:00', '1', '2015-01-30 00:00:00', '1'),
(23, 'notify_change_of_data_status', 'A _ITEM_ status has been changed by _APPROVER_NAME_', 'Hi _FIRST_NAME_,\r\n<br>\r\n<br>The status of a _ITEM_ you acted on at TMIS has been changed to _STATUS_ by _APPROVER_NAME_. This action was taken on _ACTION_DATE_. The details of the _ITEM_ are:\r\n<br>_DETAILS_\r\n<br>\r\n<br>Regards,\r\n<br>Your TMIS Admin Team\r\n<br>', 'The status of a _ITEM_ you acted on has been changed to _STATUS_ by _APPROVER_NAME_.', 'Y', '2015-01-30 00:00:00', '1', '2015-01-30 00:00:00', '1'),
(24, 'contact_us_message', 'TMIS Message From _FROMNAME_: _SUBJECT_', 'A message was sent by _FROMNAME_ at _SENT_TIME_ from _LOGIN_LINK_ with the following details:\r\n<br>\r\n<br><b>Your Name:</b> _FROMNAME_\r\n<br><b>Email Address:</b> _EMAILFROM_\r\n<br><b>Telephone:</b> _TELEPHONE_\r\n<br><b>Reason:</b> _SUBJECT_\r\n<br><b>Message:</b> \r\n<br>_DETAILS_\r\n<br>\r\n<br>Regards,\r\n<br>Your TMIS Admin Team\r\n<br>', '', 'Y', '2015-02-02 00:00:00', '1', '2015-02-02 00:00:00', '1');

-- --------------------------------------------------------

--
-- Table structure for table 'permission'
--

DROP TABLE IF EXISTS permission;
CREATE TABLE permission (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(200) NOT NULL,
  display varchar(300) NOT NULL,
  details varchar(300) NOT NULL,
  category varchar(200) NOT NULL,
  url varchar(300) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (id),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

--
-- Dumping data for table 'permission'
--

INSERT INTO permission (id, code, display, details, category, url, status) VALUES
(1, 'view_relevant_jobs', 'Relevant Jobs', '', 'job_notices', 'vacancy/relevant_list', 'active'),
(2, 'apply_for_job', 'Apply for a Job', '', 'job_notices', 'vacancy/apply', 'active'),
(3, 'view_my_saved_jobs', 'My Saved Jobs', '', 'job_notices', 'vacancy/saved', 'active'),
(4, 'view_job_application_status', 'Job Application Status', '', 'job_notices', 'vacancy/status', 'active'),
(5, 'add_new_job', 'New Job', '', 'job_notices', 'vacancy/add', 'active'),
(6, 'set_vacancy_shortlist', 'New Job Shortlist', '', 'interviews', 'vacancy/shortlist', 'active'),
(7, 'set_interview_date', 'Set Interview Date', '', 'interviews', 'interview/set_date', 'active'),
(8, 'cancel_interview', 'Cancel Interview', '', 'interviews', 'interview/cancel', 'active'),
(9, 'submit_recommendation_for_job', 'Submit Recommendation', '', 'interviews', 'interview/submit_recommendation', 'active'),
(10, 'view_recommendation_list', 'View Recommendations', 'Teacher or Manager only views their application recommendations', 'interviews', 'interview/view_recommendations', 'active'),
(11, 'add_interview_results', 'Add Interview Results', 'May include setting a date for the next interview OR marking this as final interview', 'interviews', 'interview/add_results', 'active'),
(12, 'view_interview_results', 'View Interview Results', '', 'interviews', 'interview/view_results', 'active'),
(13, 'publish_job_notices', 'Publish Jobs', '', 'approvals', 'vacancy/lists/action/publish', 'active'),
(14, 'verify_job_notices', 'Verify Jobs', '', 'approvals', 'vacancy/lists/action/verify', 'active'),
(15, 'archive_job_notices', 'Archive Jobs', '', 'approvals', 'vacancy/lists/action/archive', 'active'),
(17, 'verify_teacher_application_at_hr_level', 'HR Teacher Application Verification', '', 'approvals', 'teacher/lists/action/verify', 'active'),
(18, 'verify_teacher_application_at_instructor_level', 'Instructor Teacher Application Verification', '', 'approvals', 'teacher/lists/action/approve', 'active'),
(21, 'view_school_data_changes', 'View School Changes', '', 'approvals', 'school/lists', 'active'),
(22, 'verify_school_data_updates', 'Verify School Changes', '', 'approvals', 'school/lists/action/verify', 'active'),
(23, 'view_teacher_data_changes', 'View Teacher Changes', '', 'approvals', 'teacher/lists/action/view', 'active'),
(28, 'view_job_confirmation_applications', 'View Confirmation Applications', 'cao users only see applications for their county.', 'approvals', 'job/confirmation_applications', 'active'),
(29, 'issue_job_confirmation_letter', 'Issue Job Confirmation', '', 'approvals', 'job/confirmation_letter/action/issue', 'active'),
(30, 'verify_job_confirmation_letter', 'Verify Job Confirmation', '', 'approvals', 'job/confirmation_letter/action/verify', 'active'),
(31, 'post_to_new_position', 'Post to New Job', '', 'approvals', 'job/post', 'active'),
(32, 'view_transfer_applications', 'View Transfer Applications', 'manager only views applications for their institution', 'approvals', 'transfer/lists', 'active'),
(33, 'cancel_transfer_application', 'Cancel Transfer Application', 'teacher,manager users can only cancel their own applications', 'approvals', 'transfer/cancel', 'active'),
(34, 'verify_transfer_at_institution_level', 'Institution Transfer Verification', '', 'approvals', 'transfer/verify/level/institution', 'active'),
(35, 'verify_transfer_at_county_level', 'County Transfer Verification', '', 'approvals', 'transfer/verify/level/county', 'active'),
(36, 'submit_transfer_pca', 'Submit Transfer PCA', '', 'approvals', 'transfer/submit_pca', 'active'),
(37, 'verify_transfer_at_ministry_level', 'Ministry Transfer Verification', '', 'approvals', 'transfer/verify/level/ministry', 'active'),
(38, 'view_leave_applications', 'View Leave Applications', '', 'approvals', 'leave/lists', 'active'),
(39, 'cancel_leave_application', 'Cancel Leave Application', '', 'approvals', 'leave/cancel', 'active'),
(40, 'verify_leave_at_county_level', 'County Leave Verification', '', 'approvals', 'leave/verify/level/county', 'active'),
(41, 'verify_leave_at_ministry_level', 'Ministry Leave Verification', '', 'approvals', 'leave/verify/level/ministry', 'active'),
(42, 'prepare_leave_verification_letter', 'Send Leave Letter', '', 'approvals', 'leave/send_letter', 'active'),
(43, 'view_retirement_applications', 'View Retirement Applications', '', 'approvals', 'retirement/lists', 'active'),
(44, 'cancel_retirement_application', 'Cancel Retirement Application', '', 'approvals', 'retirement/cancel', 'active'),
(45, 'verify_retirement_application', 'Verify Retirement Application', '', 'approvals', 'retirement/verify', 'active'),
(46, 'verify_teacher_census_submissions', 'Verify Teacher Census', '', 'approvals', 'census/lists/action/verify', 'active'),
(47, 'view_teacher_census_report', 'View Teacher Census', '', 'approvals', 'census/lists', 'active'),
(48, 'complete_teacher_application', 'Complete My Teacher Application', '', 'forms', 'register/step_one', 'active'),
(49, 'update_my_teacher_profile', 'Update My Teacher Profile', '', 'forms', 'profile/teacher_data', 'active'),
(50, 'apply_for_leave', 'Apply For Leave', '', 'forms', 'leave/apply', 'active'),
(51, 'apply_for_transfer', 'Apply For Transfer', '', 'forms', 'transfer/apply', 'active'),
(52, 'request_job_confirmation', 'Request Job Confirmation', '', 'forms', 'job/request_confirmation', 'active'),
(53, 'apply_for_promotion', 'Apply For Promotion', '', 'forms', 'job/apply_for_promotion', 'active'),
(54, 'apply_to_retire', 'Apply To Retire', '', 'forms', 'retirement/apply', 'active'),
(56, 'add_new_teacher', 'New Teacher', '', 'forms', 'teacher/add', 'active'),
(57, 'submit_teacher_census_data', 'Teacher Census', '', 'forms', 'census/add', 'active'),
(58, 'add_new_school', 'New School', '', 'forms', 'school/add', 'active'),
(59, 'add_new_user', 'New User', '', 'users', 'user/add', 'active'),
(60, 'set_user_permissions', 'Set User Permissions', '', 'users', 'user/set_permissions', 'active'),
(61, 'change_user_status', 'Update User Status', '', 'users', 'user/update_status', 'active'),
(62, 'change_other_user_passwords', 'Update User Password', '', 'users', 'user/change_password', 'active'),
(63, 'send_system_message', 'New Message', '', 'my_messages', 'message/send_new_system', 'active'),
(64, 'send_email_message', 'New Email', '', 'my_messages', 'message/send_new_email', 'active'),
(65, 'send_sms_message', 'New SMS', '', 'my_messages', 'message/send_new_sms', 'active'),
(66, 'view_message_inbox', 'Inbox', '', 'my_messages', 'message/inbox', 'active'),
(67, 'view_archived_messages', 'Archived Messages', '', 'my_messages', 'message/archive', 'active'),
(68, 'view_sent_messages', 'Sent Messages', '', 'my_messages', 'message/sent', 'active'),
(69, 'view_permission_list', 'Permissions', '', 'permissions', 'permission/lists', 'active'),
(70, 'view_permission_group_list', 'Permission Groups', '', 'permissions', 'permission/group_list', 'active'),
(71, 'view_user_permissions', 'User Permissions', '', 'permissions', 'permission/user_list', 'active'),
(72, 'add_new_permission_group', 'New Permission Group', '', 'permissions', 'permission/add_group', 'active'),
(73, 'change_group_permissions', 'Update Permissions', '', 'permissions', 'permission/update_group', 'active'),
(74, 'view_user_log', 'User Activity Log', '', 'reports', 'report/lists/action/user', 'active'),
(75, 'view_system_log', 'System Log', '', 'reports', 'report/lists/action/system', 'active'),
(76, 'view_users', 'Users', '', 'reports', 'user/lists/action/report', 'active'),
(77, 'view_schools', 'Schools', '', 'reports', 'school/lists/action/report', 'active'),
(78, 'view_teachers', 'Teachers', '', 'reports', 'teacher/lists/action/report', 'active'),
(79, 'view_job_applications', 'Job Applications', '', 'reports', 'job/lists/action/report', 'active'),
(80, 'view_jobs', 'Jobs', '', 'reports', 'vacancy/lists/action/report', 'active'),
(81, 'view_retirements', 'Retirements', 'Manager only sees retirements at their school. Deo only sees retirements in their district', 'reports', 'retirement/lists/action/report', 'active'),
(82, 'view_current_job', 'Current Job', '', 'job_description', 'job/view_current', 'active'),
(83, 'view_previous_jobs', 'Previous Jobs', '', 'job_description', 'job/view_previous', 'active'),
(84, 'view_current_school', 'Current School', '', 'school_profile', 'school/view_current', 'active'),
(85, 'view_previous_schools', 'Previous Schools', '', 'school_profile', 'school/view_previous', 'active'),
(86, 'log_out', 'Log Out', '', 'log_out', 'account/logout', 'active');

-- --------------------------------------------------------

--
-- Table structure for table 'permission_group'
--

DROP TABLE IF EXISTS permission_group;
CREATE TABLE permission_group (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(300) NOT NULL,
  notes text NOT NULL,
  default_permission varchar(100) NOT NULL,
  is_removable enum('Y','N') NOT NULL DEFAULT 'Y',
  is_system_only enum('Y','N') NOT NULL DEFAULT 'N',
  date_added datetime NOT NULL,
  added_by varchar(100) NOT NULL,
  last_updated datetime NOT NULL,
  last_updated_by varchar(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

--
-- Dumping data for table 'permission_group'
--

INSERT INTO permission_group (id, name, notes, default_permission, is_removable, is_system_only, date_added, added_by, last_updated, last_updated_by) VALUES
(1, 'applicant', 'Teacher Applicant', '48', 'N', 'Y', '2015-01-01 00:00:00', '1', '0000-00-00 00:00:00', ''),
(2, 'teacher', 'Teacher', '1', 'N', 'N', '2015-01-01 00:00:00', '1', '0000-00-00 00:00:00', ''),
(3, 'manager', 'Institution Manager', '57', 'N', 'N', '2015-01-01 00:00:00', '1', '0000-00-00 00:00:00', ''),
(4, 'admin', 'Administrator', '66', 'N', 'Y', '2015-01-01 00:00:00', '1', '2015-01-31 13:12:27', '13'),
(5, 'moes', 'Ministry of Education and Sports', '78', 'N', 'N', '2015-01-01 00:00:00', '1', '0000-00-00 00:00:00', ''),
(6, 'deo', 'District Education Officer', '78', 'N', 'N', '2015-01-01 00:00:00', '1', '0000-00-00 00:00:00', ''),
(7, 'esc', 'Education Service Commission', '80', 'N', 'N', '2015-01-01 00:00:00', '1', '0000-00-00 00:00:00', ''),
(8, 'dsc', 'District Service Commission', '12', 'N', 'N', '2015-01-01 00:00:00', '1', '0000-00-00 00:00:00', ''),
(9, 'mops', 'Ministry of Public Service', '80', 'N', 'N', '2015-01-01 00:00:00', '1', '0000-00-00 00:00:00', ''),
(10, 'hr', 'Human Resource', '79', 'N', 'N', '2015-01-01 00:00:00', '1', '0000-00-00 00:00:00', ''),
(11, 'tiet', 'Teacher Instruction Education and Training', '18', 'N', 'N', '2015-01-01 00:00:00', '1', '0000-00-00 00:00:00', ''),
(12, 'cao', 'County Administrative Officer', '47', 'N', 'N', '2015-01-01 00:00:00', '1', '0000-00-00 00:00:00', ''),
(13, 'psc', 'Public Service Commission', '37', 'N', 'N', '2015-01-01 00:00:00', '1', '0000-00-00 00:00:00', '');

-- --------------------------------------------------------

--
-- Table structure for table 'permission_group_mapping'
--

DROP TABLE IF EXISTS permission_group_mapping;
CREATE TABLE permission_group_mapping (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  group_id varchar(100) NOT NULL,
  permission_id varchar(100) NOT NULL,
  date_added datetime NOT NULL,
  added_by varchar(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

--
-- Dumping data for table 'permission_group_mapping'
--

INSERT INTO permission_group_mapping (id, group_id, permission_id, date_added, added_by) VALUES
(1, '2', '1', '2015-01-20 09:46:09', ''),
(2, '3', '1', '2015-01-20 09:46:09', ''),
(4, '2', '2', '2015-01-20 09:46:09', ''),
(5, '3', '2', '2015-01-20 09:46:09', ''),
(7, '2', '3', '2015-01-20 09:46:09', ''),
(8, '3', '3', '2015-01-20 09:46:09', ''),
(10, '2', '4', '2015-01-20 09:46:09', ''),
(11, '3', '4', '2015-01-20 09:46:09', ''),
(14, '5', '5', '2015-01-20 09:46:09', ''),
(15, '6', '5', '2015-01-20 09:46:09', ''),
(16, '7', '6', '2015-01-20 09:46:09', ''),
(17, '8', '6', '2015-01-20 09:46:09', ''),
(19, '7', '7', '2015-01-20 09:46:10', ''),
(20, '8', '7', '2015-01-20 09:46:10', ''),
(22, '2', '8', '2015-01-20 09:46:10', ''),
(23, '3', '8', '2015-01-20 09:46:10', ''),
(24, '7', '8', '2015-01-20 09:46:10', ''),
(25, '8', '8', '2015-01-20 09:46:10', ''),
(29, '3', '9', '2015-01-20 09:46:10', ''),
(30, '2', '10', '2015-01-20 09:46:10', ''),
(31, '3', '10', '2015-01-20 09:46:10', ''),
(33, '7', '10', '2015-01-20 09:46:10', ''),
(34, '8', '10', '2015-01-20 09:46:10', ''),
(37, '7', '11', '2015-01-20 09:46:10', ''),
(38, '8', '11', '2015-01-20 09:46:10', ''),
(41, '7', '12', '2015-01-20 09:46:10', ''),
(42, '8', '12', '2015-01-20 09:46:10', ''),
(44, '7', '13', '2015-01-20 09:46:10', ''),
(46, '9', '14', '2015-01-20 09:46:10', ''),
(48, '5', '15', '2015-01-20 09:46:10', ''),
(49, '6', '15', '2015-01-20 09:46:10', ''),
(51, '10', '16', '2015-01-20 09:46:10', ''),
(53, '10', '17', '2015-01-20 09:46:10', ''),
(54, '11', '18', '2015-01-20 09:46:10', ''),
(69, '5', '28', '2015-01-20 09:46:10', ''),
(70, '12', '28', '2015-01-20 09:46:10', ''),
(71, '5', '29', '2015-01-20 09:46:11', ''),
(72, '12', '29', '2015-01-20 09:46:11', ''),
(74, '7', '30', '2015-01-20 09:46:11', ''),
(75, '8', '30', '2015-01-20 09:46:11', ''),
(77, '5', '31', '2015-01-20 09:46:11', ''),
(78, '12', '31', '2015-01-20 09:46:11', ''),
(80, '3', '32', '2015-01-20 09:46:11', ''),
(83, '2', '33', '2015-01-20 09:46:11', ''),
(84, '3', '33', '2015-01-20 09:46:11', ''),
(86, '3', '34', '2015-01-20 09:46:11', ''),
(87, '5', '35', '2015-01-20 09:46:11', ''),
(88, '12', '35', '2015-01-20 09:46:11', ''),
(90, '3', '36', '2015-01-20 09:46:11', ''),
(91, '13', '37', '2015-01-20 09:46:11', ''),
(93, '2', '39', '2015-01-20 09:46:11', ''),
(94, '3', '39', '2015-01-20 09:46:11', ''),
(96, '5', '40', '2015-01-20 09:46:11', ''),
(97, '12', '40', '2015-01-20 09:46:11', ''),
(99, '7', '41', '2015-01-20 09:46:11', ''),
(100, '8', '41', '2015-01-20 09:46:11', ''),
(102, '5', '42', '2015-01-20 09:46:11', ''),
(103, '12', '42', '2015-01-20 09:46:11', ''),
(106, '2', '44', '2015-01-20 09:46:11', ''),
(107, '3', '44', '2015-01-20 09:46:11', ''),
(109, '3', '45', '2015-01-20 09:46:11', ''),
(110, '5', '46', '2015-01-20 09:46:11', ''),
(111, '12', '46', '2015-01-20 09:46:11', ''),
(114, '5', '47', '2015-01-20 09:46:11', ''),
(115, '12', '47', '2015-01-20 09:46:11', ''),
(116, '1', '48', '2015-01-20 09:46:11', ''),
(117, '2', '49', '2015-01-20 09:46:11', ''),
(118, '2', '50', '2015-01-20 09:46:11', ''),
(119, '3', '50', '2015-01-20 09:46:11', ''),
(121, '2', '51', '2015-01-20 09:46:11', ''),
(122, '3', '51', '2015-01-20 09:46:11', ''),
(124, '2', '52', '2015-01-20 09:46:11', ''),
(125, '3', '52', '2015-01-20 09:46:11', ''),
(127, '2', '53', '2015-01-20 09:46:11', ''),
(128, '3', '53', '2015-01-20 09:46:11', ''),
(130, '2', '54', '2015-01-20 09:46:11', ''),
(131, '3', '54', '2015-01-20 09:46:11', ''),
(135, '10', '56', '2015-01-20 09:46:11', ''),
(137, '3', '57', '2015-01-20 09:46:11', ''),
(145, '1', '63', '2015-01-20 09:46:12', ''),
(146, '2', '63', '2015-01-20 09:46:12', ''),
(147, '3', '63', '2015-01-20 09:46:12', ''),
(149, '5', '63', '2015-01-20 09:46:12', ''),
(150, '6', '63', '2015-01-20 09:46:12', ''),
(151, '7', '63', '2015-01-20 09:46:12', ''),
(152, '8', '63', '2015-01-20 09:46:12', ''),
(153, '9', '63', '2015-01-20 09:46:12', ''),
(154, '10', '63', '2015-01-20 09:46:12', ''),
(155, '11', '63', '2015-01-20 09:46:12', ''),
(156, '12', '63', '2015-01-20 09:46:12', ''),
(157, '13', '63', '2015-01-20 09:46:12', ''),
(162, '1', '66', '2015-01-20 09:46:12', ''),
(163, '2', '66', '2015-01-20 09:46:12', ''),
(164, '3', '66', '2015-01-20 09:46:12', ''),
(166, '5', '66', '2015-01-20 09:46:12', ''),
(167, '6', '66', '2015-01-20 09:46:12', ''),
(168, '7', '66', '2015-01-20 09:46:12', ''),
(169, '8', '66', '2015-01-20 09:46:12', ''),
(170, '9', '66', '2015-01-20 09:46:12', ''),
(171, '10', '66', '2015-01-20 09:46:12', ''),
(172, '11', '66', '2015-01-20 09:46:12', ''),
(173, '12', '66', '2015-01-20 09:46:12', ''),
(174, '13', '66', '2015-01-20 09:46:12', ''),
(177, '1', '67', '2015-01-20 09:46:12', ''),
(178, '2', '67', '2015-01-20 09:46:12', ''),
(179, '3', '67', '2015-01-20 09:46:12', ''),
(181, '5', '67', '2015-01-20 09:46:12', ''),
(182, '6', '67', '2015-01-20 09:46:12', ''),
(183, '7', '67', '2015-01-20 09:46:12', ''),
(184, '8', '67', '2015-01-20 09:46:12', ''),
(185, '9', '67', '2015-01-20 09:46:12', ''),
(186, '10', '67', '2015-01-20 09:46:12', ''),
(187, '11', '67', '2015-01-20 09:46:12', ''),
(188, '12', '67', '2015-01-20 09:46:12', ''),
(189, '13', '67', '2015-01-20 09:46:12', ''),
(192, '1', '68', '2015-01-20 09:46:12', ''),
(193, '2', '68', '2015-01-20 09:46:12', ''),
(194, '3', '68', '2015-01-20 09:46:12', ''),
(196, '5', '68', '2015-01-20 09:46:12', ''),
(197, '6', '68', '2015-01-20 09:46:12', ''),
(198, '7', '68', '2015-01-20 09:46:12', ''),
(199, '8', '68', '2015-01-20 09:46:12', ''),
(200, '9', '68', '2015-01-20 09:46:12', ''),
(201, '10', '68', '2015-01-20 09:46:12', ''),
(202, '11', '68', '2015-01-20 09:46:12', ''),
(203, '12', '68', '2015-01-20 09:46:12', ''),
(204, '13', '68', '2015-01-20 09:46:12', ''),
(216, '5', '77', '2015-01-20 09:46:12', ''),
(217, '6', '77', '2015-01-20 09:46:12', ''),
(219, '5', '78', '2015-01-20 09:46:12', ''),
(220, '6', '78', '2015-01-20 09:46:12', ''),
(222, '10', '79', '2015-01-20 09:46:12', ''),
(225, '5', '80', '2015-01-20 09:46:12', ''),
(226, '6', '80', '2015-01-20 09:46:12', ''),
(227, '7', '80', '2015-01-20 09:46:12', ''),
(228, '9', '80', '2015-01-20 09:46:12', ''),
(229, '10', '80', '2015-01-20 09:46:12', ''),
(231, '3', '81', '2015-01-20 09:46:12', ''),
(233, '5', '81', '2015-01-20 09:46:12', ''),
(234, '6', '81', '2015-01-20 09:46:12', ''),
(238, '2', '82', '2015-01-20 09:46:12', ''),
(239, '3', '82', '2015-01-20 09:46:12', ''),
(241, '2', '83', '2015-01-20 09:46:12', ''),
(242, '3', '83', '2015-01-20 09:46:12', ''),
(244, '2', '84', '2015-01-20 09:46:12', ''),
(245, '3', '84', '2015-01-20 09:46:12', ''),
(247, '2', '85', '2015-01-20 09:46:12', ''),
(248, '3', '85', '2015-01-20 09:46:12', ''),
(250, '1', '86', '2015-01-20 09:46:12', ''),
(251, '2', '86', '2015-01-20 09:46:12', ''),
(252, '3', '86', '2015-01-20 09:46:12', ''),
(254, '5', '86', '2015-01-20 09:46:12', ''),
(255, '6', '86', '2015-01-20 09:46:12', ''),
(256, '7', '86', '2015-01-20 09:46:12', ''),
(257, '8', '86', '2015-01-20 09:46:12', ''),
(258, '9', '86', '2015-01-20 09:46:12', ''),
(259, '10', '86', '2015-01-20 09:46:12', ''),
(260, '11', '86', '2015-01-20 09:46:12', ''),
(261, '12', '86', '2015-01-20 09:46:12', ''),
(262, '13', '86', '2015-01-20 09:46:12', ''),
(353, '', '14', '2015-01-29 13:52:43', '13'),
(354, '', '15', '2015-01-29 13:52:43', '13'),
(355, '', '16', '2015-01-29 13:52:43', '13'),
(465, '4', '5', '2015-01-31 13:12:27', '13'),
(466, '4', '72', '2015-01-31 13:12:27', '13'),
(467, '4', '58', '2015-01-31 13:12:27', '13'),
(468, '4', '56', '2015-01-31 13:12:27', '13'),
(469, '4', '59', '2015-01-31 13:12:27', '13'),
(470, '4', '15', '2015-01-31 13:12:27', '13'),
(471, '4', '73', '2015-01-31 13:12:27', '13'),
(472, '4', '62', '2015-01-31 13:12:27', '13'),
(473, '4', '61', '2015-01-31 13:12:27', '13'),
(474, '4', '86', '2015-01-31 13:12:27', '13'),
(475, '4', '13', '2015-01-31 13:12:27', '13'),
(476, '4', '64', '2015-01-31 13:12:27', '13'),
(477, '4', '65', '2015-01-31 13:12:27', '13'),
(478, '4', '63', '2015-01-31 13:12:27', '13'),
(479, '4', '60', '2015-01-31 13:12:27', '13'),
(480, '4', '57', '2015-01-31 13:12:27', '13'),
(481, '4', '25', '2015-01-31 13:12:27', '13'),
(482, '4', '22', '2015-01-31 13:12:27', '13'),
(485, '4', '67', '2015-01-31 13:12:27', '13'),
(486, '4', '12', '2015-01-31 13:12:27', '13'),
(487, '4', '80', '2015-01-31 13:12:27', '13'),
(488, '4', '79', '2015-01-31 13:12:27', '13'),
(489, '4', '28', '2015-01-31 13:12:27', '13'),
(490, '4', '38', '2015-01-31 13:12:27', '13'),
(491, '4', '66', '2015-01-31 13:12:27', '13'),
(492, '4', '70', '2015-01-31 13:12:27', '13'),
(493, '4', '69', '2015-01-31 13:12:27', '13'),
(494, '4', '10', '2015-01-31 13:12:27', '13'),
(495, '4', '81', '2015-01-31 13:12:27', '13'),
(496, '4', '43', '2015-01-31 13:12:27', '13'),
(497, '4', '77', '2015-01-31 13:12:27', '13'),
(498, '4', '68', '2015-01-31 13:12:27', '13'),
(499, '4', '75', '2015-01-31 13:12:27', '13'),
(500, '4', '78', '2015-01-31 13:12:27', '13'),
(501, '4', '16', '2015-01-31 13:12:27', '13'),
(502, '4', '47', '2015-01-31 13:12:27', '13'),
(503, '4', '23', '2015-01-31 13:12:27', '13'),
(504, '4', '32', '2015-01-31 13:12:27', '13'),
(505, '4', '76', '2015-01-31 13:12:27', '13'),
(507, '4', '74', '2015-01-31 13:12:27', '13'),
(508, '4', '71', '2015-01-31 13:12:27', '13');

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
(3, 'add_person_data', 'INSERT INTO person (first_name, last_name, gender, marital_status, date_of_birth, date_added, citizenship_id, citizenship_type) \r\n\r\n(SELECT ''_FIRST_NAME_'' AS first_name, ''_LAST_NAME_'' AS last_name, ''_GENDER_'' AS gender, ''_MARITAL_STATUS_'' AS marital_status, ''_DATE_OF_BIRTH_'' AS date_of_birth, NOW() AS date_added, (SELECT id FROM country WHERE name=''_CITIZENSHIP_COUNTRY_'' LIMIT 1) AS citizenship_id, ''_CITIZENSHIP_TYPE_'' AS citizenship_type)'),
(4, 'add_contact_data', 'INSERT INTO contact (contact_type, carrier_id, details, parent_id, parent_type, date_added) VALUES (''_CONTACT_TYPE_'', ''_CARRIER_ID_'', ''_DETAILS_'', ''_PARENT_ID_'', ''_PARENT_TYPE_'', NOW())'),
(5, 'add_new_address', 'INSERT INTO address (parent_id, parent_type, address_type, importance, details, county, district_id, country_id, date_added)\r\n\r\n(SELECT ''_PARENT_ID_'' AS parent_id, \r\n''_PARENT_TYPE_'' AS parent_type, \r\n''_ADDRESS_TYPE_'' AS address_type, \r\n''_IMPORTANCE_'' AS importance, \r\n''_DETAILS_'' AS details, \r\n ''_COUNTY_'' AS county,\r\nIF((SELECT id FROM district WHERE name=''_DISTRICT_'' LIMIT 1) IS NOT NULL, (SELECT id FROM district WHERE name=''_DISTRICT_'' LIMIT 1), ''_DISTRICT_'') AS district_id, \r\nIF((SELECT id FROM country WHERE name=''_COUNTRY_'' LIMIT 1) IS NOT NULL, (SELECT id FROM country WHERE name=''_COUNTRY_'' LIMIT 1), ''_COUNTRY_'') AS country_id,\r\nNOW() AS date_added\r\n)'),
(6, 'add_user_data', 'INSERT INTO user (person_id, 	login_name, login_password, permission_group_id, status, date_added, last_updated) \r\n\r\n(SELECT ''_PERSON_ID_'' AS person_id, ''_LOGIN_NAME_'' AS login_name, ''_LOGIN_PASSWORD_'' AS login_password, IF(''_PERMISSION_GROUP_''<>'''', (SELECT id FROM permission_group WHERE notes=''_PERMISSION_GROUP_'' LIMIT 1), '''') AS permission_group_id, ''_STATUS_'' AS status, NOW() AS date_added, NOW() AS last_updated)'),
(7, 'get_message_template', 'SELECT * FROM message WHERE message_type=''_MESSAGE_TYPE_'''),
(8, 'add_event_log', 'INSERT INTO log (log_code, result, details, date_added) VALUES (''_LOG_CODE_'', ''_RESULT_'', ''_DETAILS_'', NOW())'),
(9, 'update_person_profile_part', 'UPDATE person SET _QUERY_PART_ WHERE id=''_PERSON_ID_'''),
(10, 'update_person_citizenship', 'UPDATE person P LEFT JOIN country C ON (C.name=''_CITIZEN_COUNTRY_'') \r\nSET P.citizenship_id=C.id, P.citizenship_type=''_CITIZENSHIP_TYPE_'' WHERE P.id=''_PERSON_ID_'''),
(11, 'add_another_id', 'INSERT INTO other_user_id (parent_id, parent_type, id_type, id_value, date_added) VALUES (''_PARENT_ID_'', ''_PARENT_TYPE_'', ''_ID_TYPE_'', ''_ID_VALUE_'', NOW())'),
(12, 'add_academic_history', 'INSERT INTO academic_history (person_id, institution, institution_type, start_date, end_date, certificate_name, certificate_number, is_highest, date_added, added_by) VALUES (''_PERSON_ID_'', ''_INSTITUTION_'', ''_INSTITUTION_TYPE_'', ''_START_DATE_'', ''_END_DATE_'', ''_CERTIFICATE_NAME_'', ''_CERTIFICATE_NUMBER_'', ''_IS_HIGHEST_'', NOW(), ''_ADDED_BY_'')'),
(13, 'add_subject_data', 'INSERT INTO subject (details, study_category, parent_id, parent_type) VALUES (''_DETAILS_'', ''_STUDY_CATEGORY_'', ''_PARENT_ID_'', ''_PARENT_TYPE_'')'),
(14, 'remove_academic_history', 'DELETE FROM academic_history WHERE person_id=''_PERSON_ID_'''),
(15, 'remove_subject_data', 'DELETE FROM subject WHERE parent_id=''_PARENT_ID_'' AND parent_type=''_PARENT_TYPE_'''),
(16, 'update_person_data', 'UPDATE person SET first_name=''_FIRST_NAME_'', last_name=''_LAST_NAME_'', gender=''_GENDER_'', date_of_birth=''_DATE_OF_BIRTH_'' WHERE id=''_PERSON_ID_'''),
(17, 'update_contact_data', 'UPDATE contact SET details=''_DETAILS_'', carrier_id=''_CARRIER_ID_''  WHERE contact_type=''_CONTACT_TYPE_'' AND parent_id=''_PARENT_ID_'' AND parent_type=''_PARENT_TYPE_'''),
(18, 'update_address_data', 'UPDATE address SET \r\n\r\ndetails=''_DETAILS_'', \r\n\r\naddress_type=''_ADDRESS_TYPE_'',\r\n \r\ncounty=''_COUNTY_'',\r\n \r\ndistrict_id=(SELECT IF((SELECT id FROM district WHERE name=''_DISTRICT_'' LIMIT 1) IS NOT NULL, (SELECT id FROM district WHERE name=''_DISTRICT_'' LIMIT 1), ''_DISTRICT_'') AS district_id), \r\n\r\ncountry_id=(SELECT IF((SELECT id FROM country WHERE name=''_COUNTRY_'' LIMIT 1) IS NOT NULL, (SELECT id FROM country WHERE name=''_COUNTRY_'' LIMIT 1), ''_COUNTRY_'') AS country_id)\r\n\r\nWHERE parent_id =''_PARENT_ID_'' AND parent_type = ''_PARENT_TYPE_'' AND importance = ''_IMPORTANCE_'''),
(19, 'update_another_id', 'UPDATE other_user_id SET id_value=''_ID_VALUE_'' WHERE parent_id=''_PARENT_ID_'' AND parent_type=''_PARENT_TYPE_'' AND id_type=''_ID_TYPE_'''),
(20, 'record_message_exchange', 'INSERT INTO message_exchange (message_id, send_format, details, subject, attachment, date_added, sender, recipient_id)\r\n\r\n(SELECT M.id AS message_id, ''_SEND_FORMAT_'' AS send_format, ''_DETAILS_'' AS details, ''_SUBJECT_'' AS subject, ''_ATTACHMENT_'' AS attachment, NOW() AS date_added, ''_SENDER_'' AS sender, U.id AS recipient_id FROM message M LEFT JOIN user U ON (U.id IN (''_RECIPIENT_ID_'')) WHERE M.message_type=''_CODE_'')'),
(21, 'get_user_by_name_and_pass', 'SELECT U.*, P.first_name, P.last_name, P.gender, P.date_of_birth, P.signature, PG.notes AS permission_group_name,\r\n\r\n(SELECT P.code FROM permission_group G LEFT JOIN permission P ON (G.default_permission=P.id) WHERE G.id=U.permission_group_id LIMIT 1) AS default_permission_code,\r\n\r\n(SELECT details FROM contact WHERE contact_type=''email'' AND parent_id=P.id AND parent_type=''person'' AND status=''active'' LIMIT 1) AS email_address,\r\n\r\nIF((SELECT details FROM contact WHERE contact_type=''telephone'' AND parent_id=P.id AND parent_type=''person'' AND status=''active'' LIMIT 1) IS NOT NULL, (SELECT details FROM contact WHERE contact_type=''telephone'' AND parent_id=P.id AND parent_type=''person'' AND status=''active'' LIMIT 1), '''') AS telephone \r\n\r\nFROM user U LEFT JOIN person P ON (U.person_id=P.id) LEFT JOIN permission_group PG ON (U.permission_group_id=PG.id)\r\nWHERE login_name=''_LOGIN_NAME_'' AND login_password=''_LOGIN_PASSWORD_'' AND status=''active'''),
(22, 'get_user_by_email', 'SELECT U.id AS user_id, P.first_name, P.last_name \r\nFROM contact C LEFT JOIN person P ON (C.parent_id=P.id AND C.contact_type=''email'' AND C.parent_type=''person'') LEFT JOIN user U ON (U.person_id=P.id) \r\nWHERE C.details=''_EMAIL_ADDRESS_'''),
(23, 'get_group_permissions', 'SELECT P.code \r\nFROM permission_group_mapping M LEFT JOIN permission P ON (M.permission_id=P.id) \r\nWHERE M.group_id=''_GROUP_ID_'''),
(24, 'activate_teacher_applicant_user', 'UPDATE user SET status=''active'', last_updated=NOW(), permission_group_id=(SELECT id FROM permission_group WHERE name=''applicant'' LIMIT 1) WHERE person_id=''_PERSON_ID_'' AND permission_group_id='''''),
(25, 'get_user_by_id', 'SELECT *, id AS user_id FROM user WHERE id=''_USER_ID_'''),
(26, 'get_group_by_id', 'SELECT * FROM permission_group WHERE id=''_GROUP_ID_'''),
(27, 'get_group_default_permission', 'SELECT P.code, P.url AS page FROM permission_group G LEFT JOIN permission P ON (G.default_permission=P.id) WHERE G.id = ''_GROUP_ID_'' LIMIT 1'),
(28, 'get_permission_by_code', 'SELECT * FROM permission WHERE code=''_CODE_'''),
(29, 'get_permission_details', 'SELECT * FROM permission WHERE code IN (_PERMISSIONS_) AND status=''active'''),
(30, 'get_user_profile', 'SELECT U.id AS user_id, U.person_id, U.login_name, U.date_added, P.first_name, P.last_name, P.signature, \r\n\r\n(SELECT notes FROM permission_group WHERE id=U.permission_group_id LIMIT 1) AS user_role, \r\n \r\n(SELECT details FROM contact WHERE parent_id=U.person_id AND parent_type=''person'' AND contact_type=''email'' AND status=''active'' LIMIT 1) AS email_address, \r\n\r\n(SELECT details FROM contact WHERE parent_id=U.person_id AND parent_type=''person'' AND contact_type=''telephone'' AND status=''active'' LIMIT 1) AS telephone \r\n\r\nFROM user U LEFT JOIN person P ON (P.id=U.person_id) \r\nWHERE U.id=''_USER_ID_'''),
(31, 'update_user_password', 'UPDATE user SET login_password=''_NEW_PASSWORD_'', previous_password=''_OLD_PASSWORD_'', last_updated=NOW(), last_updated_by=''_UPDATED_BY_'' WHERE id=''_USER_ID_'''),
(32, 'get_list_of_institutions', 'SELECT *, name AS value, name AS display FROM institution WHERE _SEARCH_QUERY_ ORDER BY name'),
(33, 'get_permission_groups', 'SELECT *, notes AS value, notes AS display FROM permission_group WHERE is_system_only IN (_SYSTEM_ONLY_) ORDER BY notes'),
(34, 'add_vacancy_data', 'INSERT INTO vacancy (school_id, start_date, end_date, topic, summary, details, role_id, date_added, added_by)\r\n\r\n(SELECT \r\n(SELECT id FROM institution WHERE name=''_INSTITUTION_'' LIMIT 1) AS school_id, \r\n\r\n''_START_DATE_'' AS start_date, ''_END_DATE_'' AS end_date, ''_TOPIC_'' AS topic, ''_SUMMARY_'' AS summary, ''_DETAILS_'', \r\n\r\n(SELECT id FROM permission_group WHERE notes=''_ROLE_'' LIMIT 1) AS role_id, \r\n\r\nNOW() AS date_added, ''_ADDED_BY_'' AS added_by\r\n)'),
(35, 'update_vacancy_data', 'UPDATE vacancy SET topic=''_TOPIC_'', summary=''_SUMMARY_'', details=''_DETAILS_'', start_date=''_START_DATE_'', end_date=''_END_DATE_'', last_updated=NOW(), last_updated_by=''_UPDATED_BY_'' WHERE id=''_VACANCY_ID_'''),
(36, 'get_vacancy_list_data', 'SELECT V.id, V.start_date, V.end_date, V.topic, V.summary, V.status, I.name AS institution_name, G.notes AS role_name \r\nFROM vacancy V LEFT JOIN institution I ON (V.school_id=I.id) LEFT JOIN permission_group G ON (V.role_id=G.id) \r\nWHERE _SEARCH_QUERY_ ORDER BY _ORDER_BY_ LIMIT _LIMIT_TEXT_;'),
(37, 'get_vacancy_by_id', 'SELECT V.*, I.name AS institution_name, G.notes AS role_name \r\nFROM vacancy V LEFT JOIN institution I ON (V.school_id=I.id) LEFT JOIN permission_group G ON (V.role_id=G.id) \r\nWHERE V.id=''_VACANCY_ID_'';'),
(38, 'add_approval_chain', 'INSERT INTO approval_chain (chain_type, step_number, subject_id, originator_id, approver_id, actual_approver, status, date_created) VALUES (''_CHAIN_TYPE_'', ''_STEP_NUMBER_'', ''_SUBJECT_ID_'', ''_ORIGINATOR_ID_'', ''_APPROVER_ID_'', ''_ACTUAL_APPROVER_'', ''_STATUS_'', NOW())'),
(39, 'get_approval_chain_setting', 'SELECT * FROM approval_chain_setting WHERE chain_type=''_CHAIN_TYPE_'' AND step_number=''_STEP_NUMBER_'''),
(40, 'get_approval_chain_by_id', 'SELECT C.*, \r\n(SELECT CONCAT(P1.first_name, '' '', P1.last_name) FROM approval_chain C1 LEFT JOIN user U1 ON (C1.originator_id ='''' AND C1.actual_approver = U1.id) LEFT JOIN person P1 ON (P1.id=U1.person_id) WHERE  C1.subject_id=C.subject_id LIMIT 1) AS originator_name, \r\n\r\nIF(C.actual_approver <> '''', (SELECT CONCAT(P2.first_name, '' '', P2.last_name) FROM user U2 LEFT JOIN person P2 ON (P2.id=U2.person_id) WHERE U2.id=C.actual_approver LIMIT 1), '''') AS approver_name \r\n\r\nFROM approval_chain C WHERE C.id=''_CHAIN_ID_'''),
(41, 'get_parties_in_chain', 'SELECT actual_approver	FROM approval_chain WHERE subject_id=''_SUBJECT_ID_'' AND status=''approved'''),
(42, 'get_originator_of_chain', 'SELECT actual_approver AS originator FROM approval_chain WHERE subject_id=''_SUBJECT_ID_'' AND step_number=''1'''),
(43, 'update_vacancy_status', 'UPDATE vacancy SET status=''_STATUS_'', last_updated=NOW(),last_updated_by=''_UPDATED_BY_'' WHERE id=''_VACANCY_ID_'''),
(44, 'deactivate_user_profile', 'UPDATE user SET status=''inactive'' WHERE id=''_USER_ID_'''),
(45, 'update_profile_visibility', 'UPDATE person SET is_visible=''_IS_VISIBLE_'' WHERE id=''_PERSON_ID_'''),
(46, 'activate_teacher_data', 'UPDATE user SET \r\n\r\npermission_group_id = (SELECT id FROM permission_group WHERE name=''teacher'' LIMIT 1), \r\nstatus=''active'', \r\nlast_updated=NOW(), last_updated_by=''_UPDATED_BY_'' \r\n\r\nWHERE id IN (_ID_LIST_)'),
(47, 'get_approver_scope', 'SELECT * FROM approval_chain_scope WHERE approver IN (_GROUP_LIST_)'),
(48, 'get_users_in_group', 'SELECT U.id AS user_id \r\nFROM user U \r\nLEFT JOIN permission_group G ON (G.id=U.permission_group_id) \r\nLEFT JOIN posting PT ON (U.id=PT.postee_id AND PT.status=''confirmed'' AND (posting_end_date=''0000-00-00'' OR posting_end_date > NOW())) \r\nLEFT JOIN institution I ON (PT.institution_id=I.id)\r\nLEFT JOIN address A ON (A.parent_type=''institution'' AND A.parent_id=I.id AND A.status IN (''active'',''verified''))\r\n\r\nWHERE G.name=''_GROUP_'' _CONDITION_'),
(49, 'get_originator_scope', 'SELECT U.id AS originator_id,\r\n\r\n(SELECT GROUP_CONCAT(PT.institution_id) FROM posting PT WHERE U.id=PT.postee_id AND PT.status IN (''pending'',''confirmed'') AND (PT.posting_end_date=''0000-00-00'' OR PT.posting_end_date > NOW())) AS institutions,\r\n\r\n(SELECT IF(A.county <> '''' AND A.county IS NOT NULL, GROUP_CONCAT(A.county), IF(A.district_id <>'''', (SELECT GROUP_CONCAT(C.name) FROM county C WHERE C.district_id=A.district_id), '''')) \r\nFROM posting PT \r\nLEFT JOIN address A ON (A.parent_type=''institution'' AND A.parent_id=PT.institution_id AND A.status IN (''active'',''verified'')) WHERE U.id=PT.postee_id AND PT.status IN (''pending'',''confirmed'') AND (PT.posting_end_date=''0000-00-00'' OR PT.posting_end_date > NOW())) AS counties,\r\n\r\n(SELECT GROUP_CONCAT(A.district_id) FROM posting PT \r\nLEFT JOIN address A ON (A.parent_type=''institution'' AND A.parent_id=PT.institution_id AND A.status IN (''active'',''verified'')) WHERE U.id=PT.postee_id AND PT.status IN (''pending'',''confirmed'') AND (PT.posting_end_date=''0000-00-00'' OR PT.posting_end_date > NOW())) AS districts\r\n \r\nFROM user U \r\nWHERE U.id=''_ORIGINATOR_ID_'''),
(50, 'get_carrier_email_domain', 'SELECT IF(mms_email_domain <>'''', mms_email_domain, sms_email_domain) AS email_domain FROM carrier WHERE number_stubs LIKE ''_NUMBER_STUB_'' OR number_stubs LIKE ''_NUMBER_STUB_,%'' OR number_stubs LIKE ''%,_NUMBER_STUB_,%'' OR number_stubs LIKE ''%,_NUMBER_STUB_'''),
(51, 'get_step_chain', 'SELECT * FROM approval_chain WHERE chain_type=''_CHAIN_TYPE_'' AND step_number=''_STEP_NUMBER_'' AND subject_id=''_SUBJECT_ID_'''),
(52, 'get_user_list_data', 'SELECT U.*, P.first_name, P.last_name, CONCAT(P.last_name, '' '', P.first_name) AS display, CONCAT(P.last_name, '' '', P.first_name) AS value,\r\n\r\n(SELECT notes FROM permission_group WHERE id=U.permission_group_id LIMIT 1) AS user_role, \r\n \r\n(SELECT details FROM contact WHERE parent_id=U.person_id AND parent_type=''person'' AND contact_type=''email'' AND status=''active'' LIMIT 1) AS email_address, \r\n\r\n(SELECT details FROM contact WHERE parent_id=U.person_id AND parent_type=''person'' AND contact_type=''telephone'' AND status=''active'' LIMIT 1) AS telephone \r\n\r\nFROM user U LEFT JOIN person P ON (P.id=U.person_id) WHERE _SEARCH_QUERY_ _ORDER_BY_ LIMIT _LIMIT_TEXT_'),
(53, 'update_user_status', 'UPDATE user SET status=''_STATUS_'', last_updated_by=''_UPDATED_BY_'', last_updated=NOW() WHERE id=''_USER_ID_'''),
(54, 'delete_user_data', 'DELETE U, P, A \r\nFROM user U \r\nJOIN person P ON (U.person_id=P.id) \r\nJOIN address A ON (A.parent_type=''person'' AND A.parent_id=P.id) \r\nWHERE U.id=''_USER_ID_'''),
(55, 'update_user_permission_group', 'UPDATE user SET permission_group_id=(SELECT id  FROM permission_group WHERE notes=''_PERMISSION_GROUP_'' LIMIT 1), last_updated_by=''_UPDATED_BY_'', last_updated=NOW() WHERE id=''_USER_ID_'''),
(56, 'get_permission_list', 'SELECT *, display AS permission FROM permission WHERE _SEARCH_QUERY_ _ORDER_BY_ LIMIT _LIMIT_TEXT_'),
(57, 'get_permission_group_list', 'SELECT G.*, P.display, \r\nIF(G.is_system_only=''Y'', ''YES'', ''NO'') AS for_system, IF(last_updated <> ''0000-00-00 00:00:00'', last_updated, '''') AS last_update_date, \r\nIF(G.last_updated_by <> '''', (SELECT CONCAT(P.last_name,'' '',P.first_name) FROM user U LEFT JOIN person P ON (U.person_id=P.id) WHERE U.id=G.last_updated_by LIMIT 1), '''') AS last_updated_user\r\n\r\nFROM permission_group G LEFT JOIN permission P ON (P.id=G.default_permission) WHERE _SEARCH_QUERY_ _ORDER_BY_ LIMIT _LIMIT_TEXT_'),
(58, 'get_user_permission_list', 'SELECT CONCAT(P.last_name, '' '', P.first_name) AS user_name, G.id AS group_id, G.notes AS group_name, PM.display AS default_permission, IF(G.last_updated <> ''0000-00-00 00:00:00'', G.last_updated, '''') AS last_update_date \r\n\r\nFROM user U \r\nLEFT JOIN person P ON (U.person_id = P.id) \r\nLEFT JOIN permission_group G ON (G.id = U.permission_group_id) \r\nLEFT JOIN permission PM ON (G.default_permission = PM.id)\r\nWHERE U.status=''active'' AND _SEARCH_QUERY_ _ORDER_BY_ LIMIT _LIMIT_TEXT_'),
(59, 'get_group_permission_list', 'SELECT M.permission_id, P.category, P.display FROM permission_group_mapping M LEFT JOIN permission P ON (M.permission_id=P.id) WHERE M.group_id=''_GROUP_ID_'' ORDER BY P.category ASC '),
(60, 'add_permission_group', 'INSERT INTO permission_group (name, notes, default_permission, is_removable, is_system_only, date_added, added_by) VALUES (''_NAME_CODE_'', ''_GROUP_NAME_'', ''_DEFAULT_PERMISSION_'', ''Y'', ''_IS_SYSTEM_ONLY_'', NOW(), ''_ADDED_BY_'')'),
(61, 'add_group_permissions', 'INSERT INTO permission_group_mapping (group_id, permission_id, date_added, added_by) \r\n(SELECT ''_GROUP_ID_'' AS group_id, P.id AS permission_id, NOW() AS date_added, ''_ADDED_BY_'' AS added_by FROM permission P WHERE P.id IN (_PERMISSION_IDS_))'),
(62, 'remove_group_permissions', 'DELETE FROM permission_group_mapping WHERE group_id=''_GROUP_ID_'''),
(63, 'update_permission_group', 'UPDATE permission_group SET default_permission=''_DEFAULT_PERMISSION_'', last_updated=NOW(), last_updated_by=''_UPDATED_BY_'' WHERE id=''_GROUP_ID_'''),
(64, 'delete_permission_group_data', 'DELETE G, M FROM permission_group G LEFT JOIN permission_group_mapping M ON ( M.group_id = G.id ) WHERE G.id=''_GROUP_ID_'''),
(65, 'get_active_users', 'SELECT * FROM user WHERE status=''active'''),
(66, 'get_user_names_by_list', 'SELECT CONCAT(P.last_name,'' '', P.first_name) AS name FROM user U LEFT JOIN person P ON (U.person_id=P.id) WHERE U.id IN (_USER_IDS_) _LIMIT_FULL_TEXT_'),
(67, 'get_message_list', 'SELECT M.*, \r\nCONCAT(P.last_name, '' '', P.first_name) AS name,\r\n\r\nIF((SELECT status FROM message_status WHERE message_exchange_id=M.id AND user_id=M.recipient_id ORDER BY date_added DESC LIMIT 1) IS NOT NULL, (SELECT status FROM message_status WHERE message_exchange_id=M.id AND user_id=M.recipient_id ORDER BY date_added DESC LIMIT 1), ''received'') AS status,\r\n\r\nM.date_added AS date_sent \r\n\r\nFROM message_exchange M \r\nLEFT JOIN contact C ON (C.contact_type=''email'' AND C.details=M.sender AND C.parent_type=''person'') \r\nLEFT JOIN person P ON (C.parent_id=P.id)\r\n\r\nWHERE M.recipient_id=''_RECIPIENT_ID_'' _SEARCH_QUERY_ _ORDER_BY_ LIMIT _LIMIT_TEXT_'),
(68, 'get_sent_message_list', 'SELECT M.*, \r\nCONCAT(P.last_name, '' '', P.first_name) AS name,\r\n\r\nIF((SELECT status FROM message_status WHERE message_exchange_id=M.id AND user_id=M.recipient_id ORDER BY date_added DESC LIMIT 1) IS NOT NULL, (SELECT status FROM message_status WHERE message_exchange_id=M.id AND user_id=M.recipient_id ORDER BY date_added DESC LIMIT 1), ''received'') AS status,\r\n\r\nM.date_added AS date_sent \r\n\r\nFROM message_exchange M \r\nLEFT JOIN user U ON (U.id=M.recipient_id)\r\nLEFT JOIN person P ON (U.person_id=P.id) \r\n\r\nWHERE M.sender=''_SENDER_EMAIL_'' _SEARCH_QUERY_ _ORDER_BY_ LIMIT _LIMIT_TEXT_'),
(69, 'add_message_status', 'INSERT INTO message_status \r\n(message_exchange_id, user_id, status, date_added) VALUES \r\n(''_MESSAGE_EXCHANGE_ID_'', ''_USER_ID_'', ''_STATUS_'', NOW()) ON DUPLICATE KEY UPDATE date_added=VALUES(date_added)'),
(70, 'get_message_by_id', 'SELECT M.*, \r\nCONCAT(P.last_name, '' '', P.first_name) AS sender_name,\r\n\r\n(SELECT CONCAT(P1.last_name,'' '', P1.first_name) FROM user U1 LEFT JOIN person P1 ON (U1.person_id=P1.id) WHERE U1.id=M.recipient_id) AS recipient_name,\r\n\r\nIF((SELECT status FROM message_status WHERE message_exchange_id=M.id ORDER BY date_added DESC LIMIT 1) IS NOT NULL, (SELECT status FROM message_status WHERE message_exchange_id=M.id ORDER BY date_added DESC LIMIT 1), ''received'') AS status,\r\n\r\nM.date_added AS date_sent,\r\nU.id AS sender_id\r\n\r\nFROM message_exchange M \r\nLEFT JOIN contact C ON (C.contact_type=''email'' AND C.details=M.sender AND C.parent_type=''person'') \r\nLEFT JOIN person P ON (C.parent_id=P.id)\r\nLEFT JOIN user U ON (U.person_id=C.parent_id AND U.login_name=M.sender)\r\n\r\nWHERE M.id=''_MESSAGE_ID_'''),
(71, 'activate_school_data', 'UPDATE school SET \r\n\r\nstatus=''active'', \r\nlast_updated=NOW(), last_updated_by=''_UPDATED_BY_'' \r\n\r\nWHERE id IN (_ID_LIST_)'),
(72, 'activate_census_data', 'UPDATE census SET \r\nstatus=''active'', \r\nlast_updated=NOW(), last_updated_by=''_UPDATED_BY_'' \r\nWHERE id IN (_ID_LIST_)'),
(73, 'get_school_by_id', 'SELECT S.*, \r\nA.details AS addressline, \r\nA.county AS county,\r\n\r\nIF((SELECT name FROM district WHERE id=A.district_id LIMIT 1) IS NOT NULL, (SELECT name FROM district WHERE id=A.district_id LIMIT 1), A.district_id) AS district,\r\n\r\nIF((SELECT name FROM country WHERE id=A.country_id LIMIT 1) IS NOT NULL, (SELECT name FROM country WHERE id=A.country_id LIMIT 1), A.country_id) AS country,\r\n\r\nC1.details AS email_address, \r\nC2.details AS telephone\r\n\r\nFROM school S \r\nLEFT JOIN address A ON (A.parent_id=S.id AND A.parent_type=''school'' AND A.is_primary=''Y'') \r\nLEFT JOIN contact C1 ON (C1.parent_id=S.id AND C1.parent_type=''school'' AND C1.is_primary=''Y'' AND C1.contact_type=''email'') \r\nLEFT JOIN contact C2 ON (C2.parent_id=S.id AND C2.parent_type=''school'' AND C2.is_primary=''Y'' AND C2.contact_type=''telephone'') \r\nWHERE S.id=''_SCHOOL_ID_'''),
(74, 'get_school_list_data', 'SELECT S.*, \r\nA.details AS addressline, \r\nA.county AS county,\r\n\r\nIF((SELECT name FROM district WHERE id=A.district_id LIMIT 1) IS NOT NULL, (SELECT name FROM district WHERE id=A.district_id LIMIT 1), A.district_id) AS district,\r\n\r\nIF((SELECT name FROM country WHERE id=A.country_id LIMIT 1) IS NOT NULL, (SELECT name FROM country WHERE id=A.country_id LIMIT 1), A.country_id) AS country,\r\n\r\nC1.details AS email_address, \r\nC2.details AS telephone\r\n\r\nFROM school S \r\nLEFT JOIN address A ON (A.parent_id=S.id AND A.parent_type=''school'' AND A.is_primary=''Y'') \r\nLEFT JOIN contact C1 ON (C1.parent_id=S.id AND C1.parent_type=''school'' AND C1.is_primary=''Y'' AND C1.contact_type=''email'') \r\nLEFT JOIN contact C2 ON (C2.parent_id=S.id AND C2.parent_type=''school'' AND C2.is_primary=''Y'' AND C2.contact_type=''telephone'') \r\nWHERE _SEARCH_QUERY_ ORDER BY _ORDER_BY_ LIMIT _LIMIT_TEXT_'),
(75, 'add_school_data', 'INSERT INTO school (name, date_registered, school_type, date_added, added_by) VALUES (''_NAME_'', ''_DATE_REGISTERED_'', ''_SCHOOL_TYPE_'', NOW(), ''_ADDED_BY_'')'),
(76, 'update_item_status', 'UPDATE _TABLE_NAME_ SET status=''_STATUS_'', last_updated_by=''_UPDATED_BY_'', last_updated=NOW() WHERE id=''_ITEM_ID_'''),
(77, 'delete_school_data', 'DELETE S, A, C FROM \r\nschool S \r\nLEFT JOIN address A ON (A.parent_id=S.id AND A.parent_type=''school'') \r\nLEFT JOIN contact C ON (C.parent_id=S.id AND C.parent_type=''school'') \r\nWHERE S.id=''_SCHOOL_ID_''\r\n'),
(78, 'get_responsibility_list', 'SELECT *, notes AS responsibility FROM responsibility ORDER BY notes ASC'),
(79, 'get_training_list', 'SELECT *, notes AS training FROM training ORDER BY type ASC, notes ASC'),
(80, 'get_census_list', 'SELECT C.*, CONCAT(P.last_name, '' '', P.first_name) AS teacher_name FROM census C LEFT JOIN user U ON (U.id=C.teacher_id) LEFT JOIN person P ON (P.id=U.person_id) \r\nWHERE _SEARCH_QUERY_ ORDER BY _ORDER_BY_ LIMIT _LIMIT_TEXT_'),
(81, 'add_census_data', 'INSERT INTO census (teacher_id, start_date, end_date, weekly_workload_average, added_by, date_added) VALUES (''_TEACHER_ID_'', ''_START_DATE_'', ''_END_DATE_'', ''_WEEKLY_WORKLOAD_AVERAGE_'', ''_ADDED_BY_'', NOW())'),
(82, 'add_census_responsibility', 'INSERT INTO census_responsibility (census_id, responsibility_id) \r\n(SELECT ''_CENSUS_ID_'' AS census_id, R.id AS responsibility_id FROM responsibility R WHERE R.id IN (_RESPONSIBILITY_IDS_))'),
(83, 'add_census_training', 'INSERT INTO census_training (census_id, training_id) \r\n(SELECT ''_CENSUS_ID_'' AS census_id, T.id AS training_id FROM training T WHERE T.id IN (_TRAINING_IDS_))'),
(84, 'remove_census_responsibility', 'DELETE FROM census_responsibility WHERE census_id=''_CENSUS_ID_'''),
(85, 'remove_census_training', 'DELETE FROM census_training WHERE census_id=''_CENSUS_ID_'''),
(86, 'get_census_responsibility_list', 'SELECT responsibility_id FROM census_responsibility WHERE census_id=''_CENSUS_ID_'''),
(87, 'get_census_training_list', 'SELECT training_id FROM census_training WHERE census_id=''_CENSUS_ID_'''),
(88, 'delete_census_data', 'DELETE C, R, T FROM \r\ncensus C \r\nLEFT JOIN census_responsibility R ON (R.census_id=C.id) \r\nLEFT JOIN census_training T ON (T.census_id=C.id) \r\nWHERE C.id = ''_CENSUS_ID_'''),
(89, 'get_census_training', 'SELECT C.*, T.code, T.notes AS training, T.type FROM census_training C LEFT JOIN training T ON (C.training_id=T.id) WHERE C.census_id=''_CENSUS_ID_'''),
(90, 'get_census_responsibility', 'SELECT C.*, R.code, R.notes AS responsibility FROM census_responsibility C LEFT JOIN responsibility R ON (C.responsibility_id=R.id) WHERE C.census_id=''_CENSUS_ID_'''),
(91, 'update_teacher_status', 'UPDATE user SET teacher_status=''_STATUS_'', last_updated=NOW(), last_updated_by=''_UPDATED_BY_'' WHERE id=''_USER_ID_'''),
(92, 'get_teacher_list_data', 'SELECT U.*, P.first_name, P.last_name, CONCAT(P.last_name, '' '', P.first_name) AS name, \r\n\r\n(SELECT I.name FROM posting P LEFT JOIN institution I ON (P.institution_id=I.id) WHERE P.postee_id=U.id ORDER BY P.posting_start_date DESC LIMIT 1) AS school,\r\n\r\n(SELECT CONCAT(A.details, '' '', A.county, '' '', (SELECT name FROM district WHERE id=A.district_id LIMIT 1), '', '', (SELECT name FROM country WHERE id=A.country_id LIMIT 1)) FROM posting P LEFT JOIN institution I ON (P.institution_id=I.id) LEFT JOIN address A ON (A.parent_id=I.id AND A.parent_type=''school'') WHERE P.postee_id=U.id ORDER BY P.posting_start_date DESC LIMIT 1) AS school_address, \r\n \r\n(SELECT details FROM contact WHERE parent_id=U.person_id AND parent_type=''person'' AND contact_type=''email'' AND is_primary=''Y'' LIMIT 1) AS email_address, \r\n\r\n(SELECT details FROM contact WHERE parent_id=U.person_id AND parent_type=''person'' AND contact_type=''telephone'' AND is_primary=''Y'' LIMIT 1) AS telephone \r\n\r\nFROM user U LEFT JOIN person P ON (P.id=U.person_id) WHERE U.permission_group_id=''2'' AND _SEARCH_QUERY_ _ORDER_BY_ LIMIT _LIMIT_TEXT_'),
(93, 'get_teacher_education', 'SELECT id AS education_id, institution_type AS institution__institutiontype, institution AS institutionname, certificate_name AS certificatename, MONTHNAME(start_date) AS from__month, YEAR(start_date) AS from__pastyear, MONTHNAME(end_date) AS to__month, YEAR(end_date) AS to__pastyear, certificate_number AS certificatenumber, is_highest AS highestcertificate FROM academic_history WHERE person_id=''_PERSON_ID_'''),
(94, 'get_teacher_subjects', 'SELECT id AS subject_id, details AS subjectname, study_category AS subject__subjecttype  FROM subject WHERE parent_id=''_PERSON_ID_'' AND parent_type=''person'''),
(95, 'get_teacher_profile', 'SELECT U.id AS teacher_id, U.person_id, P.first_name, P.last_name, CONCAT(P.last_name, '' '', P.first_name) AS name, \r\n\r\n(SELECT details FROM contact WHERE parent_id=U.person_id AND parent_type=''person'' AND contact_type=''telephone'' AND \r\n\r\nis_primary=''Y'' LIMIT 1) AS telephone, \r\n\r\n(SELECT details FROM contact WHERE parent_id=U.person_id AND parent_type=''person'' AND contact_type=''email'' AND is_primary=''Y'' LIMIT 1) AS email_address, \r\n\r\nP.gender, P.marital_status, P.date_of_birth, \r\n\r\nA1.details AS birthplace__addressline, \r\nA1.county AS birthplace__county, \r\n(SELECT name FROM district WHERE id=A1.district_id LIMIT 1) AS birthplace__district, \r\n(SELECT name FROM country WHERE id=A1.country_id LIMIT 1) AS birthplace__country, \r\nA1.address_type AS birthplace__addresstype, \r\n\r\n(SELECT id_value FROM other_user_id WHERE parent_id=U.person_id AND parent_type=''person'' AND id_type=''teacher_id'' LIMIT 1) AS teacher_id,\r\n\r\nA2.details AS permanentaddress__addressline, \r\nA2.county AS permanentaddress__county, \r\n(SELECT name FROM district WHERE id=A2.district_id LIMIT 1) AS permanentaddress__district, \r\n(SELECT name FROM country WHERE id=A2.country_id LIMIT 1) AS permanentaddress__country, \r\nA2.address_type AS permanentaddress__addresstype, \r\n\r\nA3.details AS contactaddress__addressline, \r\nA3.county AS contactaddress__county, \r\n(SELECT name FROM district WHERE id=A3.district_id LIMIT 1) AS contactaddress__district, \r\n(SELECT name FROM country WHERE id=A3.country_id LIMIT 1) AS contactaddress__country, \r\nA3.address_type AS contactaddress__addresstype, \r\n\r\n(SELECT name FROM country WHERE id=P.citizenship_id LIMIT 1) AS citizenship__country,\r\nP.citizenship_type AS citizenship__type\r\n\r\nFROM user U \r\nLEFT JOIN person P ON (P.id=U.person_id) \r\nLEFT JOIN address A1 ON (A1.parent_id=U.person_id AND A1.parent_type=''person'' AND A1.importance=''birthplace'')\r\nLEFT JOIN address A2 ON (A2.parent_id=U.person_id AND A2.parent_type=''person'' AND A2.importance=''permanent'')\r\nLEFT JOIN address A3 ON (A3.parent_id=U.person_id AND A3.parent_type=''person'' AND A3.importance=''contact'')\r\n\r\nWHERE U.id=''_TEACHER_ID_'''),
(96, 'get_teacher_grades', 'SELECT name AS value, name AS display FROM grade ORDER BY number'),
(97, 'get_grade_details_by_name', 'SELECT * FROM grade WHERE name=''_GRADE_NAME_'''),
(98, 'add_job_application', 'INSERT INTO application (vacancy_id, status, user_id, date_added, added_by, 	last_updated, last_updated_by) VALUES \r\n(''_VACANCY_ID_'', ''_STATUS_'', ''_USER_ID_'', NOW(), ''_ADDED_BY_'', NOW(), ''_ADDED_BY_'')');
















