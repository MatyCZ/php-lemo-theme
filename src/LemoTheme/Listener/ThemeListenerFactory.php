<?php

namespace LemoTheme\Listener;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ThemeListenerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ThemeListener
    {
        return new ThemeListener(
            $container->get('ThemeManager')
        );
    }
}
