<?php

namespace LemoTheme\ThemeManager;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ThemeManagerFactory implements FactoryInterface
{
    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     * @return ThemeManager
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        $options = isset($config['theme_manager']) ? $config['theme_manager'] : null;

        return new ThemeManager($container, $options);
    }

    /**
     * Create an object (v2)
     *
     * @param  ServiceLocatorInterface $container
     * @return ThemeManager
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, ThemeManager::class);
    }
}
