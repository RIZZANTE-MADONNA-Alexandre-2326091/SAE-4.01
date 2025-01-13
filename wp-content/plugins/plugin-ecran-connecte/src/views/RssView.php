<?php

namespace Views;

class RssView {
    public function render($rssFeed) {
        $output = '<h2>Linux Magazine News</h2>';
        $output .= '<ul>';
        foreach ($rssFeed->channel->item as $item) {
            $output .= '<li>';
            $output .= '<a href="' . $item->link . '">' . $item->title . '</a>';
            $output .= '<p>' . $item->description . '</p>';
            $output .= '</li>';
        }
        $output .= '</ul>';
        return $output;
    }
}