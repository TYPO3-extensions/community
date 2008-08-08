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