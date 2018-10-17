<?php

/*
 * Contao CMS (c) Leo Feyer
 *
 * @license LGPL-3.0-or-later
 */

System::loadLanguageFile('tl_content');

// Add palettes to tl_module
$GLOBALS['TL_DCA']['tl_module']['palettes']['guestbookform'] = '{type_legend},name,headline,type,gb_jumpTo;{config_legend},gb_moderate,gb_bbcode,gb_disableURL,gb_disableCaptcha;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['guestbooklist'] = '{title_legend},name,headline,type;{config_legend},gb_order,gb_perPage,gb_template;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

// Add fields to tl_module
$GLOBALS['TL_DCA']['tl_module']['fields']['gb_order'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['gb_order'],
    'default'                 => 'ascending',
    'exclude'                 => true,
    'inputType'               => 'select',
    'options'                 => array('ascending', 'descending'),
    'reference'               => &$GLOBALS['TL_LANG']['tl_module'],
    'eval'                    => array('tl_class'=>'w50'),
    'sql'                     => "varchar(32) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['gb_perPage'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['gb_perPage'],
    'exclude'                 => true,
    'inputType'               => 'text',
    'eval'                    => array('rgxp'=>'digit', 'tl_class'=>'w50'),
    'sql'                     => "smallint(5) unsigned NOT NULL default '0'"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['gb_jumpTo'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['gb_jumpTo'],
    'exclude'                 => true,
    'inputType'               => 'pageTree',
    'eval'                    => array('fieldType'=>'radio', 'tl_class'=>'clr'),
    'sql'                     => "varchar(32) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['gb_moderate'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['gb_moderate'],
    'exclude'                 => true,
    'inputType'               => 'checkbox',
    'eval'                    => array('tl_class'=>'w50'),
    'sql'                     => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['gb_bbcode'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['gb_bbcode'],
    'exclude'                 => true,
    'inputType'               => 'checkbox',
    'eval'                    => array('tl_class'=>'w50'),
    'sql'                     => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['gb_disableCaptcha'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['gb_disableCaptcha'],
    'exclude'                 => true,
    'inputType'               => 'checkbox',
    'eval'                    => array('tl_class'=>'w50'),
    'sql'                     => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['gb_disableURL'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['gb_disableURL'],
    'exclude'                 => true,
    'inputType'               => 'checkbox',
    'eval'                    => array('tl_class'=>'w50'),
    'sql'                     => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['gb_template'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['gb_template'],
    'default'                 => 'gb_default',
    'exclude'                 => true,
    'inputType'               => 'select',
    'options'                 => $this->getTemplateGroup('gb_'),
    'eval'                    => array('tl_class'=>'w50'),
    'sql'                     => "varchar(32) NOT NULL default ''"
);
?>
