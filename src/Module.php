<?php

namespace Lemo\Theme;

use Laminas\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface
{
    public function getConfig(): array
    {
        $provider = new ConfigProvider();

        return [
            'listeners' => [
                ThemeListener::class
            ],
            'service_manager' => $provider->getDependencyConfig(),
            'view_helpers' => $provider->getViewHelperConfig(),
        ];
    }
}
