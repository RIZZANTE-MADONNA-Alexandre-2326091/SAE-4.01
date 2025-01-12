<?php

namespace Views;

/**
 * Class HelpMapView
 *
 * View for the map showing interesting locations nearby
 *
 * @package Views
 */
class HelpMapView extends View
{
	/**
	 * Displays a help map in HTML format.
	 *
	 * @return string The help map content in HTML format.
	 */
    public function displayHelpMap(): string {
        return '<p>Hello, World!</p>';
    }
}