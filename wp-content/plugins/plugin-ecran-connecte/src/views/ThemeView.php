<?php

namespace Views;

class ThemeView
{
    public function renderThemeSelector(array $themes): string {
        $output = '<select id="themeSelector" onchange="changeTheme(this.value)">';
        foreach ($themes as $theme) {
            $output .= '<option value="' . $theme . '">' . ucfirst($theme) . '</option>';
        }
        $output .= '</select>';
        return $output;
    }

    public function displayThemeChangeSuccess(): void {
        echo '<p>Theme changed successfully!</p>';
    }
}