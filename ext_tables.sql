#
# Table structure for table 'fe_users'
#
CREATE TABLE fe_users (
	tx_community_sex char(1) DEFAULT '' NOT NULL,
	tx_community_nickname tinytext,
	tx_community_firstname tinytext,
	tx_community_middlename tinytext,
	tx_community_lastname tinytext,
	tx_community_mobilephone tinytext,
	tx_community_instantmessager tinytext,
	tx_community_birthday int(11) DEFAULT '0' NOT NULL,
	tx_community_activities text,
	tx_community_interests text,
	tx_community_favoritemusic text,
	tx_community_favoritetvshows text,
	tx_community_favoritemovies text,
	tx_community_favoritebooks text,
	tx_community_aboutme text
);



#
# Table structure for table 'tx_community_acl_role'
#
CREATE TABLE tx_community_acl_role (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	name tinytext,
	is_public tinyint(4) DEFAULT '0' NOT NULL,
	is_friend_role tinyint(4) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_community_acl_rule'
#
CREATE TABLE tx_community_acl_rule (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	name tinytext,
	resource tinytext,
	role text,
	access_mode int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_community_friend'
#
CREATE TABLE tx_community_friend (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	feuser text,
	friend text,
	role text,
	status varchar(50) DEFAULT '' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_community_group'
#
CREATE TABLE tx_community_group (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	name tinytext NOT NULL,
	grouptype int(11) DEFAULT '0' NOT NULL,
	description text NOT NULL,
	image text NOT NULL,
	creator int(11) DEFAULT '0' NOT NULL,
	admins int(11) DEFAULT '0' NOT NULL,
	members int(11) DEFAULT '0' NOT NULL,
	pendingmembers int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_community_group_admins_mm'
#
#
CREATE TABLE tx_community_group_admins_mm (
	uid_local int(11) DEFAULT '0' NOT NULL,
	uid_foreign int(11) DEFAULT '0' NOT NULL,
	tablenames varchar(30) DEFAULT '' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,

	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);




#
# Table structure for table 'tx_community_group_members_mm'
#
#
CREATE TABLE tx_community_group_members_mm (
	uid_local int(11) DEFAULT '0' NOT NULL,
	uid_foreign int(11) DEFAULT '0' NOT NULL,
	tablenames varchar(30) DEFAULT '' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,

	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);




#
# Table structure for table 'tx_community_group_pendingmembers_mm'
#
#
CREATE TABLE tx_community_group_pendingmembers_mm (
	uid_local int(11) DEFAULT '0' NOT NULL,
	uid_foreign int(11) DEFAULT '0' NOT NULL,
	tablenames varchar(30) DEFAULT '' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,
	isapproved int(1) DEFAULT '0' NOT NULL,

	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);



