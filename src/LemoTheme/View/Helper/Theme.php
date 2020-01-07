<?php

namespace LemoTheme\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use LemoTheme\ThemeManager\ThemeManager;
use LemoTheme\ThemeManager\ThemeManagerInterface;

class Theme extends AbstractHelper
{
    /**
     * @var ThemeManager
     */
    protected $themeManager;

    /**
     * Konstruktor
     *
     * @param ThemeManagerInterface $themeManager
     */
    public function __construct(ThemeManagerInterface $themeManager)
    {
        $this->themeManager = $themeManager;
    }

    /**
     * Vrati nazev aktualniho tema
     *
     * @return string
     */
    public function __invoke()
    {
        return $this->themeManager;
    }
}