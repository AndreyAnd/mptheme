<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
CREATE TABLE IF NOT EXISTS  wizard(
wizard_id int not null auto_increment, 
`status` INT NOT NULL DEFAULT '1',
`title` varchar(255) DEFAULT '',
`code` TEXT  DEFAULT NULL,
`scope` TEXT  DEFAULT NULL,
`image` varchar(255) DEFAULT '',
 primary key(`wizard_id`)
);
 
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 