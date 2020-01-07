<?php

namespace LemoTheme\ThemeManager;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ThemeManagerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ThemeManager
    {
        $config = $container->get('Config');
        $options = isset($config['theme_manager']) ? $config['theme_manager'] : null;

        return new ThemeManager(
            $container,
            $options
        );
    }
}
