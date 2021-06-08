<?php

namespace Lemo\Theme;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Lemo\Theme\ThemeListener;

class ThemeListenerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ThemeListener
    {
        return new ThemeListener(
            $container->get('ThemeManager')
        );
    }
}
