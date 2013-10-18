<?php

class Trio_Blog_Block_Rss_List extends Mage_Rss_Block_List {

    public function getRssMiscFeeds() {
        parent::getRssMiscFeeds();
        $this->TrioBlogFeed();
        return $this->getRssFeeds();
    }

    public function TrioBlogFeed() {
        $route = Mage::helper('blog')->getRoute() . '/rss';
        $title = Mage::getStoreConfig('blog/blog/title');
        $this->addRssFeed($route, $title);
        return $this;
    }

}
