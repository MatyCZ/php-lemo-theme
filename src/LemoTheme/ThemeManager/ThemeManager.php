<?php


namespace LemoTheme\ThemeManager;

use LemoTheme\Event\ThemeEvent;
use LemoTheme\Exception;
use Traversable;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\SplStack;

class ThemeManager implements
    ThemeManagerInterface
{
    /**
     * @var ThemeEvent
     */
    protected $event;

    /**
     * The used EventManager if any
     *
     * @var null|EventManagerInterface
     */
    protected $events = null;

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceManager;

    /**
     * Name of current theme
     *
     * @var string
     */
    protected $theme = '';

    /**
     * Name of default theme
     *
     * @var string
     */
    protected $themeDefault = 'default';

    /**
     * @var array
     */
    protected $themePaths = array();

    /**
     * List of themes
     *
     * @var array|Traversable
     */
    protected $themes = array();

    /**
     * True if modules have already been loaded
     *
     * @var bool
     */
    protected $themesAreLoaded = false;

    /**
     * Can use default theme, if current theme is not found?
     *
     * @var bool
     */
    protected $useDefaultTheme = true;

    /**
     * @param null $options
     */
    public function __construct(ServiceLocatorInterface $serviceManager, $options = null)
    {
        $this->serviceManager = $serviceManager;

        if (null !== $options) {
            $this->setOptions($options);
        }
    }

    /**
     * Configure object
     *
     * @param  array|Traversable $options
     * @return void
     * @throws Exception\InvalidArgumentException
     */
    public function setOptions($options)
    {
        if (!is_array($options) && !$options instanceof Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Expected array or Traversable object; received "%s"',
                (is_object($options) ? get_class($options) : gettype($options))
            ));
        }

        foreach ($options as $key => $value) {
            switch (strtolower($key)) {
                case 'theme':
                    $this->setTheme($value);
                    break;
                case 'theme_default':
                    $this->setThemeDefault($value);
                    break;
                case 'theme_paths':
                    $this->setThemePaths($value);
                    break;
                case 'use_default_theme':
                    $this->setUseDefaultTheme($value);
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * Inicializace ThemeManager
     */
    public function init()
    {
        // Get needed variables
        $theme = $this->getTheme();
        $themeDefault = $this->getThemeDefault();
        $themePaths = $this->getThemePaths();

        // Current theme or theme paths not given, dont init ThemeManager
        if (empty($theme) && false === $this->getUseDefaultTheme() || empty($themePaths)) {
            return;
        }

        // Load global configuration
        $config = $this->serviceManager->get('Config');

        // Load defined paths from config
        $templatePathStack = array();
        if (!empty($config['view_manager']['template_path_stack'])) {
            foreach ($config['view_manager']['template_path_stack'] as $key => $value) {
                $templatePathStack[] = $value;
            }
        }

        // Add current or default theme to paths
        $themeFound = false;
        foreach ($themePaths as $themePath) {
            $themeCurrentPath = realpath($themePath . DIRECTORY_SEPARATOR . $theme);

            if (is_dir($themeCurrentPath)) {
                $templatePathStack[] = $themeCurrentPath . DIRECTORY_SEPARATOR . 'view';
                $themeFound = true;
            }

            // Try found default theme
            if (true === $this->getUseDefaultTheme()) {
                $themeDefaultPath = realpath($themePath . DIRECTORY_SEPARATOR . $themeDefault);

                if (is_dir($themeDefaultPath)) {
                    $templatePathStack[] = $themeDefaultPath . DIRECTORY_SEPARATOR . 'view';
                    $themeDefaultFound = true;
                }
            }
        }

        // Current theme not found
        if (false === $themeFound && false === $this->getUseDefaultTheme()) {
            throw new Exception\RuntimeException(sprintf("Theme '%s' was not found", $theme));
        }

        // Current theme and default theme not found
        if (false === $themeFound & false === $themeDefaultFound && true === $this->getUseDefaultTheme()) {
            throw new Exception\RuntimeException(sprintf("Theme '%s' and default theme '%s' was not found", $theme, $themeDefault));
        }

        // Set new paths to template path stack
        $templatePathResolver = $this->serviceManager->get('ViewTemplatePathStack');
        $templatePathResolver->setPaths($templatePathStack);
    }

    /**
     * Set name of current theme to use
     *
     * @param  string $theme
     * @return ThemeManager
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Get name of current theme to use
     *
     * @return string
     */
    public function getTheme()
    {
        if (false === $this->themesAreLoaded) {
            $this->loadThemes();
        }

        return $this->theme;
    }

    /**
     * Set name of default theme
     *
     * @param  string $themeDefault
     * @return ThemeManager
     */
    public function setThemeDefault($themeDefault)
    {
        $this->themeDefault = $themeDefault;

        return $this;
    }

    /**
     * Get name of default theme
     *
     * @return string
     */
    public function getThemeDefault()
    {
        return $this->themeDefault;
    }

    /**
     * Set an array or Traversable of theme names that this theme manager should load.
     *
     * @param  array|Traversable $themes list of theme names
     * @throws Exception\InvalidArgumentException
     * @return ThemeManager
     */
    public function setThemes($themes)
    {
        if (is_array($themes) || $themes instanceof Traversable) {
            $this->themes = $themes;
            $this->themesAreLoaded = true;
        } else {
            throw new Exception\InvalidArgumentException(sprintf(
                'Parameter to %s\'s %s method must be an array or implement the Traversable interface',
                __CLASS__, __METHOD__
            ));
        }

        return $this;
    }

    /**
     * Get the array of theme names that this manager should load.
     *
     * @return array
     */
    public function getThemes()
    {
        return $this->themes;
    }

    /**
     * Load the provided themes.
     *
     * @triggers loadThemes
     * @return   ThemeManager
     */
    public function loadThemes()
    {
        if (true === $this->themesAreLoaded) {
            return $this;
        }

        $this->themesAreLoaded = true;

        return $this;
    }

    /**
     * Add a single theme path to the stack
     *
     * @param  string $themePath
     * @return ThemeManager
     * @throws Exception\InvalidArgumentException
     */
    public function addThemePath($themePath)
    {
        if (!is_string($themePath)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Invalid themePath provided; must be a string, received %s',
                gettype($themePath)
            ));
        }

        $this->themePaths[] = static::normalizeThemePath($themePath);

        return $this;
    }

    /**
     * Add many theme paths to the stack at once
     *
     * @param  array $themePaths
     * @return ThemeManager
     */
    public function addThemePaths(array $themePaths)
    {
        foreach ($themePaths as $themePath) {
            $this->addThemePath($themePath);
        }

        return $this;
    }

    /**
     * Rest the theme path stack to the themePaths provided
     *
     * @param  SplStack|array $themePaths
     * @return ThemeManager
     * @throws Exception\InvalidArgumentException
     */
    public function setThemePaths($themePaths)
    {
        if ($themePaths instanceof SplStack) {
            $this->themePaths = $themePaths;
        } elseif (is_array($themePaths)) {
            $this->clearThemePaths();
            $this->addThemePaths($themePaths);
        } else {
            throw new Exception\InvalidArgumentException(
                "Invalid argument provided for \$themePaths, expecting either an array or SplStack object"
            );
        }

        return $this;
    }

    /**
     * Returns stack of theme paths
     *
     * @return SplStack
     */
    public function getThemePaths()
    {
        return $this->themePaths;
    }

    /**
     * Clear all theme paths
     *
     * @return void
     */
    public function clearThemePaths()
    {
        $this->themePaths = new SplStack;
    }

    /**
     * Set if manager can use default theme
     *
     * @param  bool $useDefaultTheme
     * @return ThemeManager
     */
    public function setUseDefaultTheme($useDefaultTheme)
    {
        $this->useDefaultTheme = (bool) $useDefaultTheme;

        return $this;
    }

    /**
     * Can use default theme?
     *
     * @return bool
     */
    public function getUseDefaultTheme()
    {
        return $this->useDefaultTheme;
    }

    /**
     * Normalize a theme path for insertion in the stack
     *
     * @param  string $themePath
     * @return string
     */
    public static function normalizeThemePath($themePath)
    {
        $themePath = rtrim($themePath, '/');
        $themePath = rtrim($themePath, '\\');
        $themePath .= DIRECTORY_SEPARATOR;

        return $themePath;
    }
}