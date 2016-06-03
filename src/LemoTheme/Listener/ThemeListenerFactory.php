<?php

namespace LemoTheme\Listener;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ThemeListenerFactory implements FactoryInterface
{
    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     * @return ThemeListener
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $themeManager = $container->get('ThemeManager');

        return new ThemeListener($themeManager);
    }

    /**
     * Create an object (v2)
     *
     * @param  ServiceLocatorInterface $container
     * @return ThemeListener
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, ThemeListener::class);
    }
}
