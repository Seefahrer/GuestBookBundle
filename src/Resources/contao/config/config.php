<?php

/*
* Contao CMS (c) Leo Feyer
*
* @license LGPL-3.0-or-later
*/

 
use Contao\CoreBundle\Routing\ScopeMatcher;
use Symfony\Component\HttpFoundation\RequestStack;

private ScopeMatcher $scopeMatcher;
private RequestStack $requestStack;

public function __construct(ScopeMatcher $scopeMatcher, RequestStack $requestStack)
{
    $this->scopeMatcher = $scopeMatcher;
    $this->requestStack = $requestStack;
}

$request = $this->requestStack->getCurrentRequest();

if ($this->scopeMatcher->isFrontendRequest($request)) {
    $GLOBALS['TL_CSS'][]  = 'bundles/seefahrerguestbook/guestbook.min.css';
}

// Front end modules
array_insert($GLOBALS['FE_MOD'], 1, array (
    'guestbook' => array (
        'guestbooklist' => 'GuestBook',
        'guestbookform' => 'GuestBookForm'
    )
));

// Back end modules
array_insert($GLOBALS['BE_MOD']['content'], 2, array (
    'guestbook' => array (
        'tables' => array('tl_guestbook'),
        'icon'   => 'bundles/seefahrerguestbook/icon.png',
        'stylesheet' => 'bundles/seefahrerguestbook/style.css'
    )
));