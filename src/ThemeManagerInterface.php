<?php

namespace Lemo\Theme;

interface ThemeManagerInterface
{
    /**
     * Init
     */
    public function init(): void;

    /**
     * Get name of current theme.
     *
     * @return string|null
     */
    public function getTheme(): ?string;

    /**
     * Get the array of themes names that this manager should load.
     *
     * @return array
     */
    public function getThemes(): array;

    /**
     * Set an array or Traversable of theme names that this theme manager should load.
     *
     * @param  iterable $themes
     * @return self
     */
    public function setThemes(iterable $themes): self;
}
