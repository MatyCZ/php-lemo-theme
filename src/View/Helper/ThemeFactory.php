<?php

namespace Lemo\Theme\View\Helper;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ThemeFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): Theme
    {
        return new Theme(
            $container->get('ThemeManager')
        );
    }
}
