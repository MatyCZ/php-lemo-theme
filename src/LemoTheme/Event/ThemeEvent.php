<?php

namespace LemoTheme\Event;

use LemoTheme\Exception;
use Zend\EventManager\Event;

/**
 * Custom event for use with theme manager
 * Composes Theme objects
 */
class ThemeEvent extends Event
{
    /**
     * Theme events triggered by EventManager
     */
    const EVENT_CHANGE_THEME = 'changeTheme';
    const EVENT_LOAD_THEMES  = 'loadThemes';

    /**
     * @var string
     */
    protected $theme;

    /**
     * Get the name of theme
     *
     * @return string
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * Set theme name to this event
     *
     * @param  object $theme
     * @throws Exception\InvalidArgumentException
     * @return ThemeEvent
     */
    public function setTheme($theme)
    {
        if (!is_string($theme)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a string as an argument; %s provided'
                ,__METHOD__, gettype($theme)
            ));
        }

        $this->theme = $theme;

        return $this;
    }
}
