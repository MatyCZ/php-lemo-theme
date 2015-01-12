<?php

return array(
    'listeners' => array(
        'ThemeListener',
    ),
    'service_manager' => array(
        'factories' => array(
            'ThemeListener' => 'LemoTheme\Listener\ThemeListenerFactory',
            'ThemeManager'  => 'LemoTheme\ThemeManager\ThemeManagerFactory',
        )
    ),
    'view_helpers' => array(
        'factories' => array(
            'Theme'  => 'LemoTheme\View\Helper\ThemeFactory',
        )
    ),
);
