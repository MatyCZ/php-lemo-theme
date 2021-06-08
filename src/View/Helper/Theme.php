<?php

namespace Lemo\Theme\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Lemo\Theme\ThemeManagerInterface;

class Theme extends AbstractHelper
{
    protected ThemeManagerInterface $themeManager;

    public function __construct(ThemeManagerInterface $themeManager)
    {
        $this->themeManager = $themeManager;
    }

    public function __invoke(): ThemeManagerInterface
    {
        return $this->themeManager;
    }
}