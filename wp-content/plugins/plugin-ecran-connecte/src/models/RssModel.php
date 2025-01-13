<?php

namespace Models;

class RssModel {
    private $rssUrl;

    public function __construct($rssUrl) {
        $this->rssUrl = $rssUrl;
    }

    public function getRssFeed() {
        $rss = simplexml_load_file($this->rssUrl);
        return $rss;
    }
}