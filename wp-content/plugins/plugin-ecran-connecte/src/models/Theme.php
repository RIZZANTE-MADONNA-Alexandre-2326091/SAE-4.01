<?php

namespace Models;

class Theme
{
    private array $availableThemes = ['light', 'dark', 'blue'];

    public function getAvailableThemes(): array {
        return $this->availableThemes;
    }

    public function setTheme(string $theme): void {
        if (in_array($theme, $this->availableThemes)) {
            echo "<script>localStorage.setItem('selectedTheme', '$theme');</script>";
        }
    }
}