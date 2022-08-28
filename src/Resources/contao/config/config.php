<?php

/*
 * Contao CMS (c) Leo Feyer
 *
 * @license LGPL-3.0-or-later
 */

if (!System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest(System::getContainer()->get('request_stack')->getCurrentRequest())) {
    $GLOBALS['TL_CSS'][]  = 'bundles/seefahrerguestbook/guestbook.min.css';
}

// Front end modules
array_insert($GLOBALS['FE_MOD'], 1, array (
        'guestbook' => array (
            'guestbooklist' => 'GuestBook',
            'guestbookform' => 'GuestBookForm'
        )
    )
);

// Back end modules
array_insert($GLOBALS['BE_MOD']['content'], 2, array (
        'guestbook' => array (
            'tables' => array('tl_guestbook'),
            'icon'   => 'bundles/seefahrerguestbook/icon.png',
            'stylesheet' => 'bundles/seefahrerguestbook/style.css'
        )
    )
);
