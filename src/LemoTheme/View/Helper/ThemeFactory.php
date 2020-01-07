<?php

namespace LemoTheme\View\Helper;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ThemeFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): Theme
    {
        return new Theme(
            $container->get('ThemeManager')
        );
    }
}
