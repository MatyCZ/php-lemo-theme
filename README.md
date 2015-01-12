LemoTheme
====
LemoTheme is a Zend Framework 2 module that allows you to switch between various themes.
It allows you to create various themes and then switch between them. Themes can be installed in multiple folders.

The current theme can be defined in `global.ini` or `local.ini` for whole application. For the module you can set default theme
in `module.config.php`. You can switch theme from any place in application by `ServiceManager` from instance of `ThemeManager` too.

### Installation

Installation of this module uses composer. For composer documentation, please refer to
[getcomposer.org](http://getcomposer.org/).


  1. `cd my/project/directory`
  2. Create a `composer.json` file with following contents:

     ```json
     {
         "require": {
             "matycz/lemo-theme": ">=1.0"
         }
     }
     ```
  3. Run `php composer.phar install`
  4. Open `my/project/directory/config/application.config.php` and add module name `LemoTheme` into key `modules` like below:

     ```php
     return array(
         ...
         'modules' => array(
            ...
            'LemoTheme'
            ...
         ),
         ...
     );
     ```

Installation without composer is not officially supported, and requires you to install and autoload
the dependencies specified in the `composer.json`.

### Documentation

In configuration files you can set the default theme that should be used, the list of directories
that should be used for selecting the current theme as listed bellow:

    'theme_manager' => array(
        'theme' => 'default',
        'theme_paths' => array(
            './theme/'
        ),
    ),

To get a basic theme up and running you can just copy the `default` one from `examples/theme` folder into the `theme` folder in application root or create a
new one by following the tutorial bellow. Make sure that the `theme` option is set the the name of your new theme.

### Examples

```php
<?php

namespace Foo\Controller;

use LemoTheme\ThemeManager\ThemeManagerInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class BarController extends AbstractActionController
{
    /**
     * @var ThemeManagerInterface
     */
    protected $themeManager;
    
    ...

    /**
     * Page with grid example
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        // Example1 - Set new theme with name foo
        $this->getThemeManager()->setTheme('foo');

        // Example 2 - Set new theme with name foo
        $this->getServiceLocator()->get('ThemeManager')->setTheme('foor');

        return new ViewModel(array(
            ...
        ));
    }
    
    ...
    
    /**
     * @param  ThemeManagerInterface $themeManager
     * @return BarController
     */
    public function setThemeManager(ThemeManagerInterface $themeManager)
    {
        $this->themeManager = $themeManager;

        return $this;
    }

    /**
     * @return ThemeManagerInterface
     */
    public function getThemeManager()
    {
        return $this->themeManager;
    }
}
```

```php
<p class="current-theme"><?= $this->theme() ?></p>
```