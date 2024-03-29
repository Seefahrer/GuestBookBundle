<?php

/*
* Contao CMS (c) Leo Feyer
*
* @license LGPL-3.0-or-later
*/

use Contao\ArrayUtil;

// Front end styles
$GLOBALS['TL_CSS'][]  = 'bundles/seefahrerguestbook/guestbook.min.css';

// Front end modules
ArrayUtil::arrayInsert($GLOBALS['FE_MOD'], 1, array (
    'guestbook' => array (
        'guestbooklist' => 'GuestBook',
        'guestbookform' => 'GuestBookForm'
    )
));

// Back end modules
ArrayUtil::arrayInsert($GLOBALS['BE_MOD']['content'], 2, array (
    'guestbook' => array (
        'tables' => array('tl_guestbook'),
        'icon'   => 'bundles/seefahrerguestbook/icon.png',
        'stylesheet' => 'bundles/seefahrerguestbook/style.css'
    )
));