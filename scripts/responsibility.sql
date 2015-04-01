
DROP TABLE IF EXISTS responsibility;
CREATE TABLE responsibility (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(100) NOT NULL,
  notes varchar(500) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;




INSERT INTO responsibility (code, notes) VALUES
('P001', 'Head Teacher'),
('R002', 'Deputy Head Teacher'),
('R003', 'Senior Woman'),
('R004', 'Head of Dept.'),

('P001','Head Teacher'),
('P002','Deputy Head Teacher'),
('P003','Senior Woman'),
('P004','Head of Dept.'),
('P005','House Master or Mistress'),
('P006','Class Teacher/Master'),
('P007','Careers Master'),
('P008','Matron'),
('P009','Senior Man'),

('S001','Head Teacher'),
('S002','Deputy Head Teacher'),
('S003','Senior Woman'),
('S004','Head of Dept.'),
('S005','House Master or Mistress'),
('S006','Class Teacher/Master'),
('S007','Careers Master'),
('S008','Matron.'),
('S009','Senior Man'),
('B001','Principal/Director.'),
('B002','Deputy Principal/ Director'),
('B003',' Head Instructor'),
('B004','Director of Studies'),
('B005','Class teacher.'),
('B006','Games Tutor/Master'),
('B007','Head of Department'),
('B008',' Guidance & Counselling')
































