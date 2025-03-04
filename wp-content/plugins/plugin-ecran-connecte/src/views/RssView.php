<?php

namespace Views;

class RssView {
    public function render($rssFeed) {
        $output = '<div class="rss-container" style="overflow: hidden; height: 600px; position: relative;">';
        $output .= '<ul class="rss-feed" style="position: absolute; top: 0;">';
        foreach ($rssFeed->channel->item as $item) {
            $output .= '<li class="rss-item">';
            $output .= '<a class="rss-title" href="' . $item->link . '">' . $item->title . '</a>';
            $output .= '<p class="rss-description">' . $item->description . '</p>';
            $output .= '</li>';
        }
        $output .= '</ul>';
        $output .= '</div>';
        return $output;
    }
}