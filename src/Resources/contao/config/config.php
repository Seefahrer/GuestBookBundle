<?php

/*
* Contao CMS (c) Leo Feyer
*
* @license LGPL-3.0-or-later
*/

use Contao\ArrayUtil;
use Contao\System;
use Symfony\Component\HttpFoundation\Request;

// Front end styles

/*
 * -------------------------------------------------------------------------
 * FRONT END MODULES CSS Minimum Definitions
 * (ugly hack, in FE esi request, no isFrontendRequest is available)
 * -------------------------------------------------------------------------
 */


if (!System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest(System::getContainer()->get('request_stack')->getCurrentRequest() ?? Request::create('')))
{
	$GLOBALS['TL_CSS'][]  = 'bundles/seefahrerguestbook/guestbook.min.css|static';
}

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