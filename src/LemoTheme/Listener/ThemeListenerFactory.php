<?php

namespace LemoTheme\Listener;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ThemeListenerFactory implements FactoryInterface
{
    /**
     * Vytvori a vrati instanci ThemeListener
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return ThemeListener
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $themeManager = $serviceLocator->get('ThemeManager');
        return new ThemeListener($themeManager);
    }
}
