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
		`slider_auto` tinyint(1) NOT NULL default 1,
		`slider_animation` varchar(32) NOT NULL default '',
		`slider_aniduration` smallint(5) NOT NULL default '600',
		`slider_direction` varchar(32) NOT NULL default '',
		`slider_directionnav` tinyint(1) NOT NULL default 1,
		`slider_slideduration` smallint(5) NOT NULL default '7000',
		`slider_random` tinyint(1) NOT NULL default 0,
		`slider_smoothheight` tinyint(1) NOT NULL default 1,
		PRIMARY KEY (`group_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Wizard Groups';
	
	DROP TABLE IF EXISTS {$this->getTable('wizard_slide')};
	CREATE TABLE IF NOT EXISTS {$this->getTable('wizard_slide')} (
		`slide_id` smallint(6) unsigned NOT NULL auto_increment,
		`group_id` smallint(6) unsigned NOT NULL default 0,
		`title` varchar(255) NOT NULL default '',
		`url` varchar(255) NOT NULL default '',
		`url_target` varchar(255) NOT NULL default '',
		`image` varchar(255) NOT NULL default '',
		`alt_text` varchar(255) NOT NULL default '',
		`html` text NOT NULL default '',
		`sort_order` tinyint(3) NOT NULL default 1,
		`is_enabled` tinyint(1) NOT NULL default 1,
		KEY `FK_GROUP_ID_SLIDE` (`group_id`),
		CONSTRAINT `FK_GROUP_ID_SLIDE` FOREIGN KEY (`group_id`) REFERENCES `{$this->getTable('wizard_group')}` (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
		PRIMARY KEY (`slide_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Wizard Slides';

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