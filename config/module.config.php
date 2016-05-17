<?php

return [
    'listeners' => [
        'ThemeListener',
    ],
    'service_manager' => [
        'aliases' => [
            'ThemeListener' => 'LemoTheme\Listener\ThemeListener',
            'ThemeManager'  => 'LemoTheme\ThemeManager\ThemeManager',
        ],
        'factories' => [
            'LemoTheme\Listener\ThemeListener'    => 'LemoTheme\Listener\ThemeListenerFactory',
            'LemoTheme\ThemeManager\ThemeManager' => 'LemoTheme\ThemeManager\ThemeManagerFactory',
        ]
    ],
    'view_helpers' => [
        'aliases' => [
            'Theme' => 'LemoTheme\View\Helper\Theme',
        ],
        'factories' => [
            'LemoTheme\View\Helper\Theme' => 'LemoTheme\View\Helper\ThemeFactory',
        ]
    ],
];
