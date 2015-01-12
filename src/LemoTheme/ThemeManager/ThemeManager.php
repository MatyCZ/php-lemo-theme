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
    protected $theme = 'default';

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
                case 'theme_paths':
                    $this->setThemePaths($value);
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
        // Neni definovane zadne tema, nebudeme inicializovat
        $theme = $this->getTheme();
        if (empty($theme)) {
            return;
        }

        // Nacteme si konfiguraci
        $config = $this->serviceManager->get('Config');

        // Sestavime si seznam soucasnych cest k sablonam
        $templatePathStack = array();
        if (!empty($config['view_manager']['template_path_stack'])) {
            foreach ($config['view_manager']['template_path_stack'] as $key => $value) {
                $templatePathStack[] = $value;
            }
        }

        // Pokud bylo tema nalezeno, pridameho do seznamu
        $themeFound = false;
        foreach ($this->getThemePaths() as $themePath) {
            $themePath = realpath($themePath) . DIRECTORY_SEPARATOR . $this->getTheme() . DIRECTORY_SEPARATOR . 'view';
            if (is_dir($themePath)) {
                $templatePathStack[] = $themePath;
                $themeFound = true;
            }
        }

        // V pripade nenalezeni tematu, vyhodime chybu
        if (false === $themeFound) {
            throw new Exception\RuntimeException(sprintf("Theme '%s' was not found", $theme));
        }

        // Nastavime novy seznam cest
        $templatePathResolver = $this->serviceManager->get('ViewTemplatePathStack');
        $templatePathResolver->setPaths($templatePathStack);
    }

    /**
     * @param  string $theme
     * @return ThemeManager
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;

        return $this;
    }

    /**
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