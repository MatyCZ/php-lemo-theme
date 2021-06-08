<?php

namespace Lemo\Theme;

final class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
            'view_helpers' => $this->getViewHelperConfig(),
        ];
    }

    public function getDependencyConfig(): array
    {
        return [
            'aliases' => [
                'ThemeManager' => ThemeManager::class,
            ],
            'factories' => [
                ThemeListener::class => ThemeListenerFactory::class,
                ThemeManager::class => ThemeManagerFactory::class,
            ],
        ];
    }

    public function getViewHelperConfig(): array
    {
        return [
            'aliases' => [
                'theme' => View\Helper\Theme::class,
                'thememanager' => View\Helper\Theme::class,
                'themeManager' => View\Helper\Theme::class,
            ],
            'factories' => [
                View\Helper\Theme::class => View\Helper\ThemeFactory::class,
            ],
        ];
    }
}