# GuestBookBundle
Simple Contao 4.x.x Guestbook Bundle derived from former "jedoStyle/guestbook"

Copyright &#40;C&#41; 2008 - 2009 JD-WebService. All rights reserved.</br>
Author     JD-WebService</br>
License    GNU/LGPL</br>
Website    https://github.com/jedoStyle</br>

The extension has been stripped of it's avatar dependency, ICQ,MSN and Skype support.</br>
DSGVO support and link to DSGVO page has been added.

For installation add following lines to your composer.json in the root folder:

    "require": {
        ...   
        "seefahrer/contao-guestbook-bundle": "dev-master"
    },
    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/Seefahrer/GuestBookBundle"
        }
    ],
    ...
    
    
    
Subsequently either run "php composer.phar update" from the command line (terminal, putty or whatsoever),
or "Update Packages" in the Contao Manager.
