<?php

namespace LemoTheme\Listener;

use Laminas\EventManager\EventInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\Mvc\MvcEvent;
use LemoTheme\ThemeManager\ThemeManagerInterface;

class ThemeListener implements ListenerAggregateInterface
{
    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @var ThemeManagerInterface
     */
    protected $themeManager;

    /**
     * Konstruktor
     *
     * @param ThemeManagerInterface $themeManager
     */
    public function __construct(ThemeManagerInterface $themeManager)
    {
        $this->themeManager = $themeManager;
    }

    public function attach(EventManagerInterface $events, $priority = 100)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, [$this, 'initTheme'], $priority);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'initTheme'], $priority);
    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function initTheme(EventInterface $event)
    {
        $this->themeManager->init();
    }
}