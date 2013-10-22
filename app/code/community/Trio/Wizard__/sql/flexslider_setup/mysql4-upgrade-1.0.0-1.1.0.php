<?php
/**
 * @category	Trio
 * @package		Wizard
 */

	$this->startSetup();
	
	$this->getConnection()->addColumn($this->getTable('wizard_group'), 'type', " varchar(32) NOT NULL default 'basic'");
	$this->getConnection()->addColumn($this->getTable('wizard_group'), 'thumbnail_size', " smallint(5) NOT NULL default '200'");
	$this->getConnection()->addColumn($this->getTable('wizard_group'), 'theme', " varchar(32) NOT NULL default 'default'");
	$this->getConnection()->changeColumn($this->getTable('wizard_group'), 'slider_directionnav', 'nav_show', " varchar(32) NOT NULL default ''");
	$this->getConnection()->addColumn($this->getTable('wizard_group'), 'nav_style', " varchar(32) NOT NULL default ''");
	$this->getConnection()->addColumn($this->getTable('wizard_group'), 'nav_position', " varchar(32) NOT NULL default ''");
	$this->getConnection()->addColumn($this->getTable('wizard_group'), 'nav_color', " varchar(7) NOT NULL default '#666666'");
	$this->getConnection()->addColumn($this->getTable('wizard_group'), 'pagination_show', " varchar(32) NOT NULL default ''");
	$this->getConnection()->addColumn($this->getTable('wizard_group'), 'pagination_style', " varchar(32) NOT NULL default ''");
	$this->getConnection()->addColumn($this->getTable('wizard_group'), 'pagination_position', " varchar(32) NOT NULL default ''");

	$this->endSetup();
?>