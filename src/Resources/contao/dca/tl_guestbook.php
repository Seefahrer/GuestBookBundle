<?php

/*
 * Contao CMS (c) Leo Feyer
 *
 * @license LGPL-3.0-or-later
 */

use Contao\DataContainer;
use Contao\DC_Table;

$GLOBALS['TL_DCA']['tl_guestbook'] =  [
    // Config
    'config' => array (
        'dataContainer'                   => 'Table',
        'doNotCopyRecords'                => true,
        'enableVersioning'                => false,
        'closed'                          => true,
        'sql' => array (
			'keys' => array (
				'id' => 'primary',
				'published' => 'index'
			)
		)
    ),
    
    // List
    'list' => array (
        'sorting' => array (
            'mode'                    => 1,
            'fields'                  => array('date'),
            'flag'                    => 6,
            'panelLayout'             => 'filter;search,limit'
        ),
        'label' => array (
            'fields'                  => array('name'),
            'format'                  => '%s',
            'label_callback'          => array('tl_guestbook', 'listGbEntries')
        ),
        'global_operations' => array (
            'all' => array (
                'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'                => 'act=select',
                'class'               => 'header_edit_all',
                'attributes'          => 'onclick="Backend.getScrollOffset();"'
            )
        ),
        'operations' => array (
            'edit' => array (
                'label'               => &$GLOBALS['TL_LANG']['tl_guestbook']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'edit.gif'
            ),
            'delete' => array (
                'label'               => &$GLOBALS['TL_LANG']['tl_guestbook']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.gif',
                'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
            ),
            'toggle' => array (
                'label'               => &$GLOBALS['TL_LANG']['tl_guestbook']['toggle'],
                'icon'                => 'invisible.gif',
                'attributes'          => 'onclick="Backend.getScrollOffset(); return; AjaxRequest.toggleVisibility(this, %s);"',
                'button_callback'     => array('tl_guestbook', 'toggleIcon')
            ),
            'show' => array (
                'label'               => &$GLOBALS['TL_LANG']['tl_guestbook']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.gif'
            )
        )
    ),
    
    // Palettes
    'palettes' => array (
        '__selector__'                => array('redirect'),
        'default'                     => '{author_legend},name,email,place,website;{message_legend},titel,message;{date_legend:hide},tstamp,date;{comment_legend:hide},comment;{published_legend},published',
    ),

    // Subpalettes
	'subpalettes' => array (
		'addComment'                    => 'author,comment'
    ),
    
    // Fields
    'fields' => array (
        'id' => array (
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
        'name' => array (
            'label'                   => &$GLOBALS['TL_LANG']['tl_guestbook']['name'],
            'exclude'                 => true,
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>true, 'maxlength'=>128,'tl_class'=>'w50'),
            'sql'                     => "varchar(128) NOT NULL default ''"
        ),
        'email' => array (
            'label'                   => &$GLOBALS['TL_LANG']['tl_guestbook']['email'],
            'exclude'                 => true,
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>true, 'maxlength'=>128, 'rgxp'=>'email', 'decodeEntities'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(128) NOT NULL default ''"
        ),
        'place' => array (
            'label'                   => &$GLOBALS['TL_LANG']['tl_guestbook']['place'],
            'exclude'                 => true,
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>true, 'maxlength'=>128, 'tl_class'=>'w50'),
            'sql'                     => "varchar(128) NOT NULL default ''"
        ),
        'website' => array (
            'label'                   => &$GLOBALS['TL_LANG']['tl_guestbook']['website'],
            'exclude'                 => true,
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('maxlength'=>128, 'rgxp'=>'url', 'decodeEntities'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(128) NOT NULL default ''"
        ),
        'titel' => array (
            'label'                   => &$GLOBALS['TL_LANG']['tl_guestbook']['titel'],
            'exclude'                 => true,
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>true,'maxlength'=>128),
            'sql'                     => "varchar(128) NOT NULL default ''"
        ),
        'message' => array (
            'label'                   => &$GLOBALS['TL_LANG']['tl_guestbook']['message'],
            'exclude'                 => true,
            'inputType'               => 'textarea',
            'eval'                    => array('rte'=>'tinyMCE'),
            'sql'                     => "text NULL"
        ),
        'comment' => array (
            'label'                   => &$GLOBALS['TL_LANG']['tl_guestbook']['comment'],
            'exclude'                 => true,
            'inputType'               => 'textarea',
            'eval'                    => array('rte'=>'tinyMCE'),
            'sql'                     => "text NULL"
        ),
		'date' => array (
			'label'                   => &$GLOBALS['TL_LANG']['tl_guestbook']['date'],
			'default'                 => time(),
			'exclude'                 => true,
			'filter'                  => true,
			'flag'                    => 8,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'date', 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
            'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'tstamp' => array (
			'label'                   => &$GLOBALS['TL_LANG']['tl_guestbook']['tstamp'],
			'default'                 => time(),
			'exclude'                 => true,
			'filter'                  => true,
			'flag'                    => 8,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'date', 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
            'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
        'published' => array (
            'label'                   => &$GLOBALS['TL_LANG']['tl_guestbook']['published'],
            'exclude'                 => true,
            'filter'                  => true,
            'inputType'               => 'checkbox',
            'eval'                    => array('doNotCopy'=>true),
            'sql'                     => "char(1) NOT NULL default ''"
        )
    )
];
class tl_guestbook extends Backend {
    // Database result 
    protected $arrData = null;
    
    // List Guestbook entries
    public function listGbEntries($arrRow) {
        if (is_null($this->arrData)) {
            $objData = $this->Database->execute("SELECT id, name FROM tl_guestbook");
            while ($objData->next()) {
                $this->arrData[$objData->id] = $objData->name;
            }
        }
        $key = $arrRow['published'] ? 'published' : 'unpublished';
        
        // Backend Output
        return '
        <div class="guestbook_wrap">
        <div class="cte_type ' . $key . '"><strong><a href="mailto:' . Idna::decodeEmail($arrRow['email']) . '" title="' . StringUtil::specialchars(Idna::decodeEmail($arrRow['email'])) . '">' . $arrRow['name'] . '</a></strong> - ' . Date::parse(Config::get('dateFormat'), $arrRow['date']) . '<br>' . $title . '</div>
        <div class="limit_height mark_links' . (!Config::get('doNotCollapse') ? ' h30' : '') . ' block"><strong>' . '"' . $arrRow['titel'] . '</strong>' . '<br/><br/>' . $arrRow['message'] . '</div></div>' . "\n    ";
    }
    
    // Toggle published icon
    public function toggleIcon($row, $href, $label, $title, $icon, $attributes) {
        if (mb_strlen($this->Input->get('id'))) {
            $this->toggleVisibility($this->Input->get('id'), ($this->Input->get('state') == 1));
            $this->redirect($this->getReferer());
        }
        $href .= '&id='.$row['id'].'&state='.$row['published'];
        if ($row['published']) { $icon = 'visible.gif'; }
        return '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ';
    }
   // Toggle publsihed on Database
    public function toggleVisibility($intId, $blnVisible) {
        // Update database
        $this->Database->prepare("UPDATE tl_guestbook SET published='" . ($blnVisible ? '' : 1) . "' WHERE id=?")
        ->execute($intId);
    }
}
