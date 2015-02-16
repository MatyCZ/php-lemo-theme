<?php

namespace LemoTheme\Listener;

use LemoTheme\ThemeManager\ThemeManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventInterface;
use Zend\Mvc\MvcEvent;
use Zend\View\ViewEvent;

class ThemeListener implements ListenerAggregateInterface
{
    /**
     * @var array
     */
    protected $listeners = array();

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

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'initTheme'), 5555);
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