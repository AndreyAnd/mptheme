<?php
/**
 * @package	Wizard
 * @author     callmeandrey@gmail.com
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
		`sort_order` smallint(5) NULL default 0,
		`is_active` tinyint(1) NOT NULL default 1,
                `type` varchar(32) NOT NULL default 'basic',                
                
                
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
		`view` varchar(255) NOT NULL default 'grid',
		`image` varchar(255) NOT NULL default '',
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