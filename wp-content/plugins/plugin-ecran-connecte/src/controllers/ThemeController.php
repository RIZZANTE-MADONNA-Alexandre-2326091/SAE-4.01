<?php

namespace Controllers;

use Models\Theme;
use Views\ThemeView;

class ThemeController extends Controller
{
    private Theme $model;
    private ThemeView $view;

    public function __construct() {
        $this->model = new Theme();
        $this->view = new ThemeView();
    }

    public function changeTheme(string $theme): void {
        $this->model->setTheme($theme);
        $this->view->displayThemeChangeSuccess();
    }

    public function displayThemeSelector(): string {
        $themes = $this->model->getAvailableThemes();
        return $this->view->renderThemeSelector($themes);
    }
}