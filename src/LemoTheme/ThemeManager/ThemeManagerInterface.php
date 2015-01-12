<?php

namespace LemoTheme\ThemeManager;

use Traversable;

/**
 * Theme manager interface
 */
interface ThemeManagerInterface
{
    /**
     * Get name of current theme.
     *
     * @return string
     */
    public function getTheme();

    /**
     * Get the array of themes names that this manager should load.
     *
     * @return array
     */
    public function getThemes();

    /**
     * Set an array or Traversable of theme names that this theme manager should load.
     *
     * @param  array|Traversable $themes
     * @return ThemeManagerInterface
     */
    public function setThemes($themes);
}
