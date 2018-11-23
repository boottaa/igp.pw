<?php
/**
 * Created by PhpStorm.
 * User: b.akhmedov
 * Date: 07.11.18
 * Time: 10:40
 */
//https://docs.zendframework.com/tutorials/navigation/
//$this->navigation('Zend\Navigation\Admin')->menu()->setUlClass('widget widget-menu unstyled')
return [
    'default' => [
        [
            'label' => 'Home',
            'route' => 'home',
            'class' => 'nav-link',
        ],
        [
            'label' => 'About IGP',
            'route' => 'igp',
            'class' => 'nav-link',
            'action' => 'about'
        ],
        [
            'label' => 'News',
            'route' => 'igp',
            'class' => 'nav-link',
            'action' => 'news'
        ],
        [
            'label' => 'Login',
            'route' => 'igp',
            'class' => 'nav-link nav-login',
            'action' => 'login',
            'visible' => true
        ],
        [
            'label' => 'Admin',
            'route' => 'admin',
            'class' => 'nav-link nav-admin',
            'visible' => false
        ],
        [
            'label' => 'Logout',
            'route' => 'igp',
            'class' => 'nav-link nav-logout',
            'action' => 'logout',
            'visible' => false
        ],
    ],

    'admin_links' => [
        [
            //Информация по ссылкам (колечество переходов, интерактивная карта и количество переходов с разных стран и городов)

            'label' => 'Dashboard',
            'icon' => 'menu-icon icon-dashboard',
            'route' => 'admin',
            'class' => 'nav-link',
        ],

        [
            'label' => 'My links',
            'route' => 'admin',
            'class' => 'nav-link',
            'action' => 'mylinks',
        ],
        [
            'label' => 'Portfolio',
            'route' => 'admin',
            'class' => 'nav-link',
            'action' => 'portfolio',
        ],
    ],
];