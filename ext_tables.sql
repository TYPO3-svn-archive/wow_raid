#
# Table structure for table 'tx_wowraid_raids'
#
CREATE TABLE tx_wowraid_raids (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	fe_group int(11) DEFAULT '0' NOT NULL,
	instance int(11) DEFAULT '0' NOT NULL,
	begin int(11) DEFAULT '0' NOT NULL,
	prepare int(11) DEFAULT '0' NOT NULL,
	participants blob NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_wowraid_comments'
#
CREATE TABLE tx_wowraid_comments (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	raid int(11) NOT NULL,
	author int(11) NOT NULL,
	message text NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);