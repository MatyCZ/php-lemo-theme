<?php

namespace Lemo\Theme;

use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Mvc\MvcEvent;

class ThemeListener extends AbstractListenerAggregate
{
    /**
     * @var ThemeManagerInterface
     */
    protected ThemeManagerInterface $themeManager;

    public function __construct(ThemeManagerInterface $themeManager)
    {
        $this->themeManager = $themeManager;
    }

    public function attach(EventManagerInterface $events, $priority = 100): void
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, [$this, 'initTheme'], $priority);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'initTheme'], $priority);
    }

    public function initTheme(EventInterface $event): void
    {
        $this->themeManager->init();
    }
}