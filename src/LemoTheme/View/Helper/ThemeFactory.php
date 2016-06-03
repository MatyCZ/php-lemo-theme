<?php

namespace LemoTheme\View\Helper;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ThemeFactory implements FactoryInterface
{
    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     * @return Theme
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $themeManager = $container->get('ThemeManager');
        return new Theme($themeManager);
    }

    /**
     * Create an object (v2)
     *
     * @param  ServiceLocatorInterface $containerInterface
     * @return Theme
     */
    public function createService(ServiceLocatorInterface $containerInterface)
    {
        return $this($containerInterface, Theme::class);
    }
}
