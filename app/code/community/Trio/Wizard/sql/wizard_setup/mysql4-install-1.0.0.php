<?php
/**
 * @category	Trio
 * @package		Wizard
 */

$installer = $this;

$installer->startSetup();

$installer->run("

	DROP TABLE IF EXISTS {$this->getTable('wizard_group')};		
	CREATE TABLE IF NOT EXISTS {$this->getTable('wizard_group')} (
		`group_id` smallint(6) unsigned NOT NULL auto_increment,
		`title` varchar(255) NOT NULL default '',
		`code` varchar(32) NOT NULL default '',
		`position` varchar(128) NOT NULL default '',
		`width` smallint(4) default NULL,
		`sort_order` smallint(5) NULL default 0,
		`is_active` tinyint(1) NOT NULL default 1,
                `type` varchar(32) NOT NULL default 'basic',
                `thumbnail_size` smallint(5) NOT NULL default '200',
                `theme`  varchar(32) NOT NULL default 'default',                
                `nav_show`  varchar(32) NOT NULL default '',
                `nav_style`  varchar(32) NOT NULL default '',
                `nav_position`  varchar(32) NOT NULL default '',
                `nav_color` varchar(7) NOT NULL default '#666666',
                `pagination_show`  varchar(32) NOT NULL default '',
                `pagination_style`  varchar(32) NOT NULL default '',
                `pagination_position`   varchar(32) NOT NULL default '',
                `hosted_image`  tinyint(1) NOT NULL default 0,
                `hosted_image_url` varchar(512) NOT NULL default '',
                `hosted_image_thumburl`  varchar(512) NOT NULL default '',
                `wizard_pauseonaction`  tinyint(1) NOT NULL default 1 ,
                `wizard_pauseonhover` tinyint(1) NOT NULL default 0,
		`wizard_auto` tinyint(1) NOT NULL default 1,
		`wizard_animation`  varchar(32) NOT NULL default '',
		`wizard_aniduration` smallint(5) NOT NULL default '600',
		`wizard_direction`  varchar(32) NOT NULL default '',
		`wizard_directionnav` tinyint(1) NOT NULL default 1,
		`wizard_wizardduration` smallint(5) NOT NULL default '7000',
		`wizard_random` tinyint(1) NOT NULL default 0,
		`wizard_smoothheight` tinyint(1) NOT NULL default 1,
		PRIMARY KEY (`group_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Wizard Groups';
	
	DROP TABLE IF EXISTS {$this->getTable('wizard_wizard')};
	CREATE TABLE IF NOT EXISTS {$this->getTable('wizard_wizard')} (
		`wizard_id` int unsigned NOT NULL auto_increment,  
                `status` tinyint(3) NOT NULL DEFAULT '1',
                `code` TEXT  NOT NULL default '',
                `scope` TEXT  NOT NULL default '',
		`group_id` smallint(6) unsigned NOT NULL default 0,
		`title` varchar(255) NOT NULL default '',
		`url` varchar(255) NOT NULL default '',
		`url_target` varchar(255) NOT NULL default '',
		`image` varchar(255) NOT NULL default '',
		`alt_text` varchar(255) NOT NULL default '',
		`html` text NOT NULL default '',
		`sort_order` tinyint(3) NOT NULL default 1,
		`is_enabled` tinyint(1) NOT NULL default 1,
		KEY `FK_GROUP_ID_WIZARD` (`group_id`),
		CONSTRAINT `FK_GROUP_ID_WIZARD` FOREIGN KEY (`group_id`) REFERENCES `{$this->getTable('wizard_group')}` (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
		PRIMARY KEY (`wizard_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Wizard Wizards';

	DROP TABLE IF EXISTS {$this->getTable('wizard_category')};
	CREATE TABLE IF NOT EXISTS {$this->getTable('wizard_category')} (
		`group_id` smallint(6) NOT NULL,
		`category_id` smallint(6) NOT NULL,
		PRIMARY KEY (`group_id`,`category_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Wizard Categories';

	DROP TABLE IF EXISTS {$this->getTable('wizard_page')};
	CREATE TABLE IF NOT EXISTS {$this->getTable('wizard_page')} (
		`group_id` smallint(6) NOT NULL,
		`page_id` smallint(6) NOT NULL,
		PRIMARY KEY (`group_id`,`page_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Wizard Pages';

	DROP TABLE IF EXISTS {$this->getTable('wizard_store')};
	CREATE TABLE IF NOT EXISTS {$this->getTable('wizard_store')} (
		`group_id` smallint(6) NOT NULL,
		`store_id` smallint(6) NOT NULL,
		PRIMARY KEY (`group_id`,`store_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Wizard Stores';

");

$installer->endSetup();