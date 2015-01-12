<?php

namespace LemoTheme\Listener;

use LemoTheme\ThemeManager\ThemeManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventInterface;
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
        $this->listeners[] = $events->getSharedManager()->attach(
            'Zend\View\View', ViewEvent::EVENT_RENDERER, array($this, 'initTheme'), 10000
        );
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