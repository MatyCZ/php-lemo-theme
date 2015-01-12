<?php

namespace LemoTheme\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ThemeFactory implements FactoryInterface
{
    /**
     * Vytvori a vrati instanci Theme
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return Theme
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $themeManager = $serviceLocator->getServiceLocator()->get('ThemeManager');
        return new Theme($themeManager);
    }
}
