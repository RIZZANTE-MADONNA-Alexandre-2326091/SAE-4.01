<?php

namespace Controllers;

use Models\RssModel;
use Views\RssView;

class RssController extends Controller {
    private $model;
    private $view;

    public function __construct() {
        $this->model = new RssModel('https://www.linux-magazine.com/rss/feed/lmi_news');
        $this->view = new RssView();
    }

    public function displayRssFeed() {
        $rssFeed = $this->model->getRssFeed();
        return $this->view->render($rssFeed);
    }
}