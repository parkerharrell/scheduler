DROP TABLE IF EXISTS `{PRFX}users`;
CREATE TABLE IF NOT EXISTS `{PRFX}users` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(128) NOT NULL,
  `email` varchar(128) NOT NULL,
  `password` varchar(64) NOT NULL,
  `first_name` varchar(128) NULL,
  `last_name` varchar(128) NULL,
  `created` int(11) NOT NULL,
  `lang` VARCHAR(16),
  PRIMARY KEY  (`id`)
);

DROP TABLE IF EXISTS `{PRFX}objectmeta`;
CREATE TABLE IF NOT EXISTS `{PRFX}objectmeta` (
	`id` int(11) NOT NULL auto_increment,
	`obj_class` varchar(32) NOT NULL,
	`obj_id` int(11) NOT NULL,
	`meta_name` varchar(32) NOT NULL,
	`meta_value` TEXT,
	`meta_data` TEXT,
	PRIMARY KEY  (`id`)
);

DROP TABLE IF EXISTS `{PRFX}conf`;
CREATE TABLE IF NOT EXISTS `{PRFX}conf` (
	`id` int(11) NOT NULL auto_increment,
	`name` varchar(255) NOT NULL,
	`value` TEXT,
	PRIMARY KEY  (`id`)
);

DROP TABLE IF EXISTS `{PRFX}templates`;
CREATE TABLE IF NOT EXISTS `{PRFX}templates` (
	`id` int(11) NOT NULL auto_increment,
	`lang` varchar(16) NOT NULL,
	`template` varchar(255) NOT NULL,
	`subject` varchar(255) NOT NULL,
	`body` TEXT,
	PRIMARY KEY  (`id`)
);

DROP TABLE IF EXISTS `{PRFX}emaillog`;
CREATE TABLE IF NOT EXISTS `{PRFX}emaillog` (
	`id` int(11) NOT NULL auto_increment,
	`sent_at` int(11),

	`to_email` VARCHAR(128),
	`from_email` VARCHAR(128),
	`from_name` VARCHAR(128),
	`subject` TEXT,
	`body` TEXT,
	`alt_body` TEXT,

	PRIMARY KEY  (`id`)
	);

DROP TABLE IF EXISTS `{PRFX}forms`;
CREATE TABLE IF NOT EXISTS `{PRFX}forms` (
	`id` int(11) NOT NULL auto_increment,

	`title` VARCHAR(128),
	`class` VARCHAR(32),
	`details` VARCHAR(128),

	PRIMARY KEY  (`id`)
	);
	
DROP TABLE IF EXISTS `{PRFX}form_controls`;
CREATE TABLE IF NOT EXISTS `{PRFX}form_controls` (
	`id` int(11) NOT NULL auto_increment,

	`form_id` int(11) NOT NULL,
	`name` VARCHAR(128),
	`type` VARCHAR(32),
	`title` VARCHAR(255),
	`description` TEXT,
	`show_order` int(11),

	`ext_access` VARCHAR(32),

	`attr` TEXT,
	`validators` TEXT,
	`default_value` TEXT,

	PRIMARY KEY  (`id`)
	);

DROP TABLE IF EXISTS `{PRFX}services`;
CREATE TABLE IF NOT EXISTS `{PRFX}services` (
	`id` int(11) NOT NULL auto_increment,

	`title` VARCHAR(255),
	`description` TEXT,

	`min_from_now` int(11) DEFAULT 0,
	`max_from_now` int(11) DEFAULT 1209600,
	`min_cancel` int(11) DEFAULT 86400,

	`allow_queue` TINYINT DEFAULT 0,
	`pack_only` TINYINT DEFAULT 0,
	`class_type` TINYINT DEFAULT 0,

	`duration` int(11) DEFAULT 1800,
	`until_closed` TINYINT DEFAULT 0,
	`lead_in` int(11) DEFAULT 0,
	`lead_out` int(11) DEFAULT 0,
	`price` VARCHAR(16) DEFAULT '',

	`recur_total` int(11) DEFAULT 1,
	`recur_options` VARCHAR(64) DEFAULT 'd-2d-w',

	`return_url` TEXT,

	`show_order` int(11) DEFAULT 1,
	PRIMARY KEY  (`id`)
	);

DROP TABLE IF EXISTS `{PRFX}service_cats`;
CREATE TABLE IF NOT EXISTS `{PRFX}service_cats` (
	`id` int(11) NOT NULL auto_increment,

	`title` VARCHAR(255),
	`description` TEXT,

	`show_order` int(11) DEFAULT 1,
	PRIMARY KEY  (`id`)
	);

DROP TABLE IF EXISTS `{PRFX}locations`;
CREATE TABLE IF NOT EXISTS `{PRFX}locations` (
	`id` int(11) NOT NULL auto_increment,

	`title` VARCHAR(255),
	`description` TEXT,
	`show_order` int(11) DEFAULT 1,

	PRIMARY KEY  (`id`)
	);

DROP TABLE IF EXISTS `{PRFX}resources`;
CREATE TABLE IF NOT EXISTS `{PRFX}resources` (
	`id` int(11) NOT NULL auto_increment,

	`title` VARCHAR(255),
	`description` TEXT,
	`show_order` int(11) DEFAULT 1,

	PRIMARY KEY  (`id`)
	);

DROP TABLE IF EXISTS `{PRFX}schedules`;
CREATE TABLE IF NOT EXISTS `{PRFX}schedules` (
	`id` int(11) NOT NULL auto_increment,
	`resource_id` int(11) NOT NULL,

	`title` VARCHAR(255),

	`valid_from` int(11) DEFAULT 0,
	`valid_to` int(11) DEFAULT 0,

	`capacity` int(11) DEFAULT 1,

	PRIMARY KEY  (`id`)
	);

DROP TABLE IF EXISTS `{PRFX}timeblocks`;
CREATE TABLE IF NOT EXISTS `{PRFX}timeblocks` (
	`id` int(11) NOT NULL auto_increment,

	`schedule_id` int(11) NOT NULL,
	`starts_at` int(11) NOT NULL,
	`ends_at` int(11) NOT NULL,
	`selectable_every` int(11) NOT NULL,
	`selectable_fixed` TEXT,

	`applied_on` int(11) NOT NULL,
	PRIMARY KEY  (`id`)
	);

DROP TABLE IF EXISTS `{PRFX}timeoffs`;
CREATE TABLE IF NOT EXISTS `{PRFX}timeoffs` (
	`id` int(11) NOT NULL auto_increment,

	`resource_id` int(11) NOT NULL,
	`location_id` int(11) NOT NULL,

	`starts_at` int(11) NOT NULL,
	`ends_at` int(11) NOT NULL,

	`description` TEXT,
	PRIMARY KEY  (`id`)
	);

DROP TABLE IF EXISTS `{PRFX}appointments`;
CREATE TABLE IF NOT EXISTS `{PRFX}appointments` (
	`id` int(11) NOT NULL auto_increment,

	`service_id` int(11) NOT NULL,
	`resource_id` int(11) NOT NULL,
	`customer_id` int(11) NOT NULL,
	`location_id` int(11) NOT NULL,
	`seats` int(11) NOT NULL DEFAULT 1,

	`created_at` int(11) NOT NULL,
	`starts_at` int(11) NOT NULL,
	`duration` int(11) NOT NULL,
	`lead_in` int(11) NOT NULL,
	`lead_out` int(11) NOT NULL,
	`until_closed` TINYINT DEFAULT 0,

	`approved` tinyint NOT NULL,
	`no_show` tinyint NOT NULL,
	`auth_code` varchar(32) NOT NULL DEFAULT '',
	`price` VARCHAR(16) DEFAULT '',
	`need_reminder` int(11) NOT NULL DEFAULT 0,

	`is_ghost` tinyint NOT NULL DEFAULT 0,
	`cancelled` tinyint NOT NULL DEFAULT 0,
	`ghost_last_access` int(11) NOT NULL DEFAULT 0,
	PRIMARY KEY  (`id`)
	);

DROP TABLE IF EXISTS `{PRFX}invoices`;
CREATE TABLE IF NOT EXISTS `{PRFX}invoices` (
	`id` int(11) NOT NULL auto_increment,
	`refno` VARCHAR(16),

	`amount` DOUBLE,
	`currency` VARCHAR(3),

	`created_at` int(11),
	PRIMARY KEY  (`id`)
	);

DROP TABLE IF EXISTS `{PRFX}payments`;
CREATE TABLE IF NOT EXISTS `{PRFX}payments` (
	`id` int(11) NOT NULL auto_increment,
	`invoice_id` int(11),

	`paid_at` int(11),
	`amount_gross` DOUBLE,
	`amount_net` DOUBLE,
	`currency` VARCHAR(3),

	`pgateway` VARCHAR(32),
	`pgateway_ref` TEXT,
	`pgateway_response` TEXT,

	PRIMARY KEY  (`id`)
	);	

DROP TABLE IF EXISTS `{PRFX}packs`;
CREATE TABLE IF NOT EXISTS `{PRFX}packs` (
	`id` int(11) NOT NULL auto_increment,

	`title` VARCHAR(255),
	`description` TEXT,

	`discount` TEXT,
	`services` TEXT,

	`show_order` int(11) DEFAULT 1,
	PRIMARY KEY  (`id`)
	);
