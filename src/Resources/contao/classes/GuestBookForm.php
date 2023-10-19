<?php

/*
* Contao CMS (c) Leo Feyer
*
* @license LGPL-3.0-or-later
*/


//namespace Seefahrer\GuestBookBundle\Module;

use Contao\FrontendUser;
use Contao\Functions;
use Contao\Input;
use Contao\Message;
use Contao\Module;
use Contao\StringUtil;
use Contao\System;
use Contao\Widget;

//namespace Seefahrer\GuestBookBundle\GuestBookForm;

class GuestBookForm extends Module {   
    
    protected $strTemplate = 'mod_guestbookform';

    public function generate() {
        if (System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest(System::getContainer()->get('request_stack')->getCurrentRequest())) {
            $objTemplate = new BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### GUESTBOOK FORM ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao?do=modules&act=edit&id=' . $this->id;
            return $objTemplate->parse();
        }
        return parent::generate();
    }
    /**
    * Generate module
    */
    protected function compile() {
        // Get front end user object
        $User = FrontendUser::getInstance();
        // Access control
        if ($this->protected && !System::getContainer()->get('contao.security.token_checker')->hasBackendUser()) {
            if (!System::getContainer()->get('contao.security.token_checker')->hasFrontendUser()) {
                $this->Template->protected = true;
                return;
            }
            $arrGroups = StringUtil::deserialize($this->groups);
            if (is_array($arrGroups) && count(array_intersect($User->groups, $arrGroups)) < 1) {
                $this->Template->protected = true;
                return;
            }
        }
        // Form fields
        $arrFields = array (
            'name' => array (
                'label' => $GLOBALS['TL_LANG']['GUESTBOOK']['gb_name'],
                'name' => 'gbname',
                'value' => trim($User->firstname . ' ' . $User->lastname),
                'inputType' => 'text',
                'eval' => array('mandatory'=>true, 'maxlength'=>128)
            ),
            'place' => array (
                'label' => $GLOBALS['TL_LANG']['GUESTBOOK']['gb_place'],
                'name' => 'gbplace',
                'value' => $User->place,
                'inputType' => 'text',
                'eval' => array('mandatory'=>true, 'maxlength'=>128)
            ),
            'email' => array (
                'label' => $GLOBALS['TL_LANG']['GUESTBOOK']['gb_email'],
                'name' => 'gbemail',
                'value' => $User->email,
                'inputType' => 'text',
                'eval' => array('rgxp'=>'email', 'mandatory'=>true, 'maxlength'=>128, 'decodeEntities'=>true, 'tl_class'=>'w50')
            )
        );
        if (!$this->gb_disableURL) {
            $arrFields['website'] = array (
                'label' => $GLOBALS['TL_LANG']['GUESTBOOK']['gb_website'],
                'name' => 'gbwebsite',
                'value' => $User->website,
                'inputType' => 'text',
                'eval' => array('rgxp'=>'url', 'maxlength'=>128, 'decodeEntities'=>true,'tl_class'=>'w50')
            );
        }
        // Title field
        $arrFields['titel'] = array (
            'label' => $GLOBALS['TL_LANG']['GUESTBOOK']['gb_titel'] ,
            'name' => 'gbtitel',
            'inputType' => 'text',
            'eval' => array( 'mandatory'=>true, 'maxlength'=>128)
        );
        // Comment field
        $arrFields['message'] = array (
            'label' => $GLOBALS['TL_LANG']['GUESTBOOK']['gb_message'] ,
            'name' => 'gbmessage',
            'inputType' => 'textarea',
            'eval' => array('rows'=>10, 'cols'=>75, 'allowHtml'=>true)
        );
        // Captcha
		if (!$this->gb_disableCaptcha) {
			$arrFields['captcha'] = array (
				'name'      => 'gbcaptcha',
				'label'     => $GLOBALS['TL_LANG']['GUESTBOOK']['gb_captcha'],
				'inputType' => 'captcha',
				'eval'      => array('mandatory'=>true)
			);
		}
        if ($this->gb_moderate) {
            $this->Template->moderate = true;
        }
        if ($this->gb_bbcode) {
            $this->Template->bbcode = true;
        }
        $doNotSubmit = false;
        $arrWidgets = array();

        // Initialize widgets
        foreach ($arrFields as $arrField) {
            $strClass = $GLOBALS['TL_FFL'][$arrField['inputType']];
            // Continue if the class is not defined
            if (!class_exists($strClass)) {
                continue;
            }
            $arrField['eval']['required'] = $arrField['eval']['mandatory'];
            $objWidget = new $strClass(Widget::getAttributesFromDca($arrField, $arrField['name'], $arrField['value']));
            // Validate widget
            $session = Contao\System::getContainer()->get('request_stack')->getCurrentRequest()->getSession();
            dd($session->get('contao.form.data')->getValue()['formId']);
            if (Input::post('FORM_SUBMIT') == 'tl_guestbook') {
                $objWidget->validate();
                if ($objWidget->hasErrors()) {
                    $doNotSubmit = true;
                }
            }
            $arrWidgets[] = $objWidget;
        }
        $this->Template->fields = $arrWidgets;
        $this->Template->submit = $GLOBALS['TL_LANG']['GUESTBOOK']['submit'];
        $this->Template->action = StringUtil::ampersand($this->Environment->request);
        $this->Template->messages = Message::generate(false, false);

        // Confirmation message
        if ($_SESSION['TL_GUESTBOOKENTRIE_ADDED']) {
            $this->Template->confirm = $GLOBALS['TL_LANG']['GUESTBOOK']['confirm'];
            $_SESSION['TL_GUESTBOOKENTRIE_ADDED'] = false;
        }
        // Add comment
        if (Input::post('FORM_SUBMIT') == 'tl_guestbook' && !$doNotSubmit) {
            $this->addGbEntrie();
            // Pending for approval
            if ($this->gb_moderate) {
                $_SESSION['TL_GUESTBOOKENTRIE_ADDED'] = true;
            }
            $this->reload();
        }
    }
    
    /**
    * Replace bbcode and add the comment to the database
    *
    * Supports the following tags:
    *
    * - [b][/b] bold
    * - [i][/i] italic
    * - [u][/u] underline
    * - [img][/img]
    * - [code][/code]
    * - [quote][/quote]
    * - [quote=tim][/quote]
    * - [url][/url]
    * - [url=https://][/url]
    * - [email][/email]
    * - [email=name@domain.com][/email]
    */
    protected function addGbEntrie() {

        // Get Form Data
        $formData = System::getContainer()->get('request_stack')->getCurrentRequest()->getSession();
		
        $strWebsite = $formData->get('contao.form.data')->getValue()['gbwebsite'];
        // Add https:// to website
        if (strlen($strWebsite) && !preg_match('@^https?://|ftp://|mailto:@i', $strWebsite)) {
            $strWebsite = 'https://' . $strWebsite;
        }
        $strComment = trim($formData->get('contao.form.data')->getValue()['gbmessage']);
        // Replace bbcode
        if ($this->gb_bbcode) {
            $arrSearch = array (
                '[b]', '[/b]',
                '[i]', '[/i]',
                '[u]', '[/u]',
                '[code]', '[/code]',
                '[quote]', '[/quote]',
                ':grin',
                ':)',
                ':-)',
                ':p',
                ';)',
                ';-)',
                ':(',
                ':cry',
                ':eek',
                ':o)',
                '8)',
                ':?',
                ':x',
                ':roll',
                ':zzz',
                ':sigh',
                ':upset'
            );
            $arrReplace = array (
                '<strong>', '</strong>',
                '<em>', '</em>',
                '<span style="text-decoration:underline;">', '</span>',
                '<div class="code"><p>' . $GLOBALS['TL_LANG']['MSC']['com_code'] . '</p><pre>', '</pre></div>',
                '<div class="quote">', '</div>',
                '<img src="assets/tinymce4/js/plugins/emoticons/img/smiley-smile.gif">',
                '<img src="assets/tinymce4/js/plugins/emoticons/img/smiley-smile.gif">',
                '<img src="assets/tinymce4/js/plugins/emoticons/img/smiley-smile.gif">',
                '<img src="assets/tinymce4/js/plugins/emoticons/img/smiley-laughing.gif">',
                '<img src="assets/tinymce4/js/plugins/emoticons/img/smiley-wink.gif">',
                '<img src="assets/tinymce4/js/plugins/emoticons/img/smiley-wink.gif">',
                '<img src="assets/tinymce4/js/plugins/emoticons/img/smiley-frown.gif">',
                '<img src="assets/tinymce4/js/plugins/emoticons/img/smiley-cry.gif">',
                '<img src="assets/tinymce4/js/plugins/emoticons/img/smiley-surprised.gif">',
                '<img src="assets/tinymce4/js/plugins/emoticons/img/smiley-surprised.gif">',
                '<img src="assets/tinymce4/js/plugins/emoticons/img/smiley-cool.gif">',
                '<img src="assets/tinymce4/js/plugins/emoticons/img/smiley-embarassed.gif">',
                '<img src="assets/tinymce4/js/plugins/emoticons/img2/sm_dead.gif">',
                '<img src="assets/tinymce4/js/plugins/emoticons/img2/sm_rolleyes.gif">',
                '<img src="assets/tinymce4/js/plugins/emoticons/img2/sm_sleep.gif">',
                '<img src="assets/tinymce4/js/plugins/emoticons/img2/sm_sigh.gif">',
                '<img src="assets/tinymce4/js/plugins/emoticons/img2/sm_upset.gif">'
            );
            $strComment = str_replace($arrSearch, $arrReplace, $strComment);
            $strComment = preg_replace('/\[color=([^\]]+)\]/i', '<span style="color:$1;">', $strComment);
            $strComment = preg_replace('/\[quote=([^\]]+)\]/i', '<div class="quote"><p>' . sprintf($GLOBALS['TL_LANG']['MSC']['com_quote'], '$1') . '</p>', $strComment);
            $strComment = preg_replace('/\[img\]([^\[]+)\[\/img\]/i', '<img src="$1" alt="" />', $strComment);
            $strComment = preg_replace('/\[url\]([^\[]+)\[\/url\]/i', '<a href="$1">$1</a>', $strComment);
            $strComment = preg_replace('/\[url=([^\]]+)\]([^\[]+)\[\/url\]/i', '<a href="$1">$2</a>', $strComment);
            $strComment = preg_replace('/\[email\]([^\[]+)\[\/email\]/i', '<a href="mailto:$1">$1</a>', $strComment);
            $strComment = preg_replace('/\[email=([^\]]+)\]([^\[]+)\[\/email\]/i', '<a href="mailto:$1">$2</a>', $strComment);
            $strComment = preg_replace(array('@</div>(\n)*@', '@\r@'), array("</div>\n", ''), $strComment);
        }
        // Encode e-mail addresses
        if (strpos($strComment, 'mailto:') !== false) {
            $strComment = StringUtil::encodeEmail($strComment);
        }
        // Prevent cross-site request forgeries
        $strComment = preg_replace('/(href|src|on[a-z]+)="[^"]*(typolight\/main\.php|javascript|vbscri?pt|script|alert|document|cookie|window)[^"]*"+/i', '$1="#"', $strComment);
        // Prepare record
        $arrSet = array (
            'tstamp' => time(),
            'name' => $formData->get('contao.form.data')->getValue()['gbname'],
            'place' => $formData->get('contao.form.data')->getValue()['gbplace'],
            'email' => $formData->get('contao.form.data')->getValue()['gbemail'],
            'website' => '',
            'titel' => $formData->get('contao.form.data')->getValue()['gbtitel'],
            'message' => functions::nl2br_pre($strComment),
            'place' => '',
            'date' => time(),
            'published' => 1
        );
        // Moderate
        if ($this->gb_moderate) {
            $arrSet['published'] = '';
        }
        if (!$this->gb_disableURL) {
            $arrSet['website'] = $strWebsite;
        }

        $insertId = $this->Database->prepare("INSERT INTO tl_guestbook %s")->set($arrSet)->execute()->insertId;

        // Inform admin
        // Notification
        $objEmail = new Email();

        $objEmail->from = $GLOBALS['TL_ADMIN_EMAIL'];
        $objEmail->fromName = $GLOBALS['TL_ADMIN_NAME'];
        $objEmail->subject = sprintf($GLOBALS['TL_LANG']['MSC']['email_subject'], $this->Environment->host);

        // Convert the comment to plain text
        $strComment = strip_tags($strComment);
        $strComment = StringUtil::decodeEntities($strComment);
        $strComment = str_replace(array('&', '<', '>'), array('&', '<', '>'), $strComment);

        // Add comment details
        $objEmail->text = sprintf($GLOBALS['TL_LANG']['MSC']['com_message'],
                                  $arrSet['name'] . ' (' . $arrSet['email'] . ')',
                                  $strComment,
                                  $this->Environment->base . $this->Environment->request,
                                  $this->Environment->base . 'contao?do=guestbook&act=edit&id=' . $insertId);

        $objEmail->sendTo($GLOBALS['TL_ADMIN_EMAIL']);

        // Redirect if there is a jumpTo page
        if ($this->gb_jumpTo) {
            /* $objNextPage = $this->Database->prepare("SELECT id, alias FROM tl_page WHERE id=?")->limit(1)->execute($this->gb_jumpTo);
            if ($objNextPage->numRows) {
                $params = $this->$objNextPage->fetchAssoc()['alias'];
                $this->redirect($this->getFrontendUrl($params));
            } 
            if ($objNextPage->numRows)
            {
                $this->redirect($this->generateFrontendUrl($objNextPage->fetchAssoc()));
            }*/
            $this->jumpToOrReload($this->gb_jumpTo);
        }
    }
}
