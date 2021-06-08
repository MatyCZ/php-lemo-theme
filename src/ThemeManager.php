<?php


namespace Lemo\Theme;

use Interop\Container\ContainerInterface;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Stdlib\SplStack;
use Lemo\Theme\Exception;
use Traversable;

use function is_dir;
use function realpath;
use function rtrim;
use function sprintf;

class ThemeManager implements ThemeManagerInterface
{
    /**
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * Name of current skin
     *
     * @var string|null
     */
    protected ?string $skin = null;

    /**
     * Name of current theme
     *
     * @var string|null
     */
    protected ?string $theme = null;

    /**
     * Name of default theme
     *
     * @var string|null
     */
    protected ?string $themeDefault = null;

    /**
     * Name of current theme group
     *
     * @var string|null
     */
    protected ?string $themeGroup = null;

    /**
     * @var array
     */
    protected array $themePaths = [];

    /**
     * List of themes
     *
     * @var array
     */
    protected array $themes = [];

    /**
     * True if modules have already been loaded
     *
     * @var bool
     */
    protected bool $themesAreLoaded = false;

    /**
     * Can use default theme, if current theme is not found?
     *
     * @var bool
     */
    protected bool $useDefaultTheme = true;

    /**
     * Can use groups for themes?
     *
     * @var bool
     */
    protected bool $useGroups = false;

    /**
     * @param ContainerInterface $container
     * @param iterable           $options
     */
    public function __construct(ContainerInterface $container, iterable $options = [])
    {
        $this->container = $container;

        if (!empty($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * Configure object
     *
     * @param  iterable $options
     * @return self
     */
    public function setOptions(iterable $options): self
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (isset($options['skin'])) {
            $this->setSkin($options['skin']);
        }

        if (isset($options['theme'])) {
            $this->setTheme($options['theme']);
        }

        if (isset($options['theme_default'])) {
            $this->setThemeDefault($options['theme_default']);
        }

        if (isset($options['theme_paths'])) {
            $this->setThemePaths($options['theme_paths']);
        }

        if (isset($options['use_default_theme'])) {
            $this->setUseDefaultTheme($options['use_default_theme']);
        }

        if (isset($options['use_groups'])) {
            $this->setUseGroups($options['use_groups']);
        }

        return $this;
    }

    /**
     * Init
     */
    public function init(): void
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
        $config = $this->container->get('Config');

        // Load defined paths from config
        $templatePathStack = [];
        if (!empty($config['view_manager']['template_path_stack'])) {
            foreach ($config['view_manager']['template_path_stack'] as $value) {
                $templatePathStack[] = $value;
            }
        }

        // Add current or default theme to paths
        $themeFound = false;
        $themeDefaultFound = false;
        foreach ($themePaths as $themePath) {

            // Generate themes dir base path
            $themeBasePath = $themePath;
            if ($this->getUseGroups() && !empty($this->getThemeGroup())) {
                $themeBasePath .= $this->getThemeGroup() . DIRECTORY_SEPARATOR;
            }

            $themeCurrentPath = realpath($themeBasePath . $theme);

            // Try found default theme
            if (true === $this->getUseDefaultTheme()) {
                $themeDefaultPath = realpath($themeBasePath . $themeDefault);

                if (!empty($themeDefaultPath) && is_dir($themeDefaultPath)) {
                    $templatePathStack[] = $themeDefaultPath . DIRECTORY_SEPARATOR . 'view';
                    $themeDefaultFound = true;
                }
            }

            // Current theme must be set as last
            if (is_dir($themeCurrentPath)) {
                $templatePathStack[] = $themeCurrentPath . DIRECTORY_SEPARATOR . 'view';
                $themeFound = true;
            }
        }

        // Current theme not found
        if (false === $themeFound && false === $this->getUseDefaultTheme()) {
            throw new Exception\RuntimeException(
                sprintf(
                    "Theme '%s' was not found",
                    $theme
                )
            );
        }

        // Current theme and default theme not found
        if (false === $themeFound && false === $themeDefaultFound && true === $this->getUseDefaultTheme()) {
            throw new Exception\RuntimeException(
                sprintf(
                    "Theme '%s' and default theme '%s' was not found",
                    $theme,
                    $themeDefault
                )
            );
        }

        // Set new paths to template path stack
        $templatePathResolver = $this->container->get('ViewTemplatePathStack');
        $templatePathResolver->setPaths($templatePathStack);
    }

    /**
     * Set name of current skin to use
     *
     * @param  string|null $skin
     * @return self
     */
    public function setSkin(?string $skin): self
    {
        $this->skin = $skin;

        return $this;
    }

    /**
     * Get name of current skin to use
     *
     * @return string|null
     */
    public function getSkin(): ?string
    {
        return $this->skin;
    }

    /**
     * Set name of current theme to use
     *
     * @param  string|null $theme
     * @return self
     */
    public function setTheme(?string $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Get name of current theme to use
     *
     * @return string|null
     */
    public function getTheme(): ?string
    {
        if (false === $this->themesAreLoaded) {
            $this->loadThemes();
        }

        return $this->theme;
    }

    /**
     * Set name of default theme
     *
     * @param  string|null $themeDefault
     * @return self
     */
    public function setThemeDefault(?string $themeDefault): self
    {
        $this->themeDefault = $themeDefault;

        return $this;
    }

    /**
     * Get name of default theme
     *
     * @return string|null
     */
    public function getThemeDefault(): ?string
    {
        return $this->themeDefault;
    }

    /**
     * Set name of theme group
     *
     * @param  string|null $themeGroup
     * @return self
     */
    public function setThemeGroup(?string $themeGroup): self
    {
        $this->themeGroup = $themeGroup;

        return $this;
    }

    /**
     * Get name of theme group
     *
     * @return string|null
     */
    public function getThemeGroup(): ?string
    {
        return $this->themeGroup;
    }

    /**
     * Set an array or Traversable of theme names that this theme manager should load.
     *
     * @param iterable $themes list of theme names
     * @return self
     * @throws Exception\InvalidArgumentException
     */
    public function setThemes(iterable $themes): self
    {
        if ($themes instanceof Traversable) {
            $themes = ArrayUtils::iteratorToArray($themes);
        }

        $this->themes = $themes;
        $this->themesAreLoaded = true;

        return $this;
    }

    /**
     * Get the array of theme names that this manager should load.
     *
     * @return array
     */
    public function getThemes(): array
    {
        return $this->themes;
    }

    /**
     * Load the provided themes.
     *
     * @triggers loadThemes
     * @return   self
     */
    public function loadThemes(): self
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
     * @return self
     */
    public function addThemePath(string $themePath): self
    {
        $this->themePaths[] = $this->normalizeThemePath($themePath);

        return $this;
    }

    /**
     * Rest the theme path stack to the themePaths provided
     *
     * @param  SplStack|array $themePaths
     * @return self
     */
    public function setThemePaths(iterable $themePaths): self
    {
        if ($themePaths instanceof Traversable) {
            $themePaths = ArrayUtils::iteratorToArray($themePaths);
        }

        $this->themePaths = [];
        foreach ($themePaths as $themePath) {
            $this->addThemePath($themePath);
        }

        return $this;
    }

    /**
     * Returns stack of theme paths
     *
     * @return array
     */
    public function getThemePaths(): array
    {
        return $this->themePaths;
    }

    /**
     * Clear all theme paths
     *
     * @return self
     */
    public function clearThemePaths(): self
    {
        $this->themePaths = [];

        return $this;
    }

    /**
     * Set if manager can use default theme
     *
     * @param  bool $useDefaultTheme
     * @return self
     */
    public function setUseDefaultTheme(bool $useDefaultTheme): self
    {
        $this->useDefaultTheme = $useDefaultTheme;

        return $this;
    }

    /**
     * Can use default theme?
     *
     * @return bool
     */
    public function getUseDefaultTheme(): bool
    {
        return $this->useDefaultTheme;
    }

    /**
     * Set if manager can use groups for themes
     *
     * @param  bool $useGroups
     * @return self
     */
    public function setUseGroups(bool $useGroups): self
    {
        $this->useGroups =  $useGroups;

        return $this;
    }

    /**
     * Can use groups for themes?
     *
     * @return bool
     */
    public function getUseGroups(): bool
    {
        return $this->useGroups;
    }

    /**
     * Normalize a theme path for insertion in the stack
     *
     * @param  string $themePath
     * @return string
     */
    private function normalizeThemePath(string $themePath): string
    {
        $themePath = rtrim($themePath, '/');
        $themePath = rtrim($themePath, '\\');
        $themePath .= DIRECTORY_SEPARATOR;

        return $themePath;
    }
}