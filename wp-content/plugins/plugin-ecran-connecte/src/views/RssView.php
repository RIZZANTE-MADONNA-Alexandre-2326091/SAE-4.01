<?php

namespace Views;

class RssView {
    public function render($rssFeed) {
        $output = '<ul class="rss-feed">';
        foreach ($rssFeed->channel->item as $item) {
            $output .= '<li class="rss-item">';
            $output .= '<a class="rss-title" href="' . $item->link . '">' . $item->title . '</a>';
            $output .= '<p class="rss-description">' . $item->description . '</p>';
            $output .= '</li>';
        }
        $output .= '</ul>';
        return $output;
    }
}