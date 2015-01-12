<?php

namespace LemoTheme\ThemeManager;

use LemoTheme\ThemeManager\ThemeManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ThemeManagerFactory implements FactoryInterface
{
    /**
     * Creates and returns the theme manager
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return ThemeManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $options = isset($config['theme_manager']) ? $config['theme_manager'] : null;

        return new ThemeManager($serviceLocator, $options);
    }
}
