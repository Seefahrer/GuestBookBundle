# GuestBookBundle
Simple Contao 4.x.x Guestbook Bundle derived from former "jedo/guestbook"

Copyright &#40;C&#41; 2008 - 2009 JD-WebService. All rights reserved.
Author     JD-WebService
License    GNU/LGPL
Website    https://github.com/jedoStyle

The extension has been stripped of it's avatar dependency, ICQ,MSN and Skype support.

For installation add following lines to your composer.json in the root folder:

    "require": {
        ...   
        "jedo/contao-guestbook-bundle": "dev-master"
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/Seefahrer/GuestBookBundle"
        }
    ],
    ...
    
    
    
Subsequently either run "php composer.phar update" from the command line (terminal, putty or whatsoever),
or "Update Packages" in the Contao Manager.
