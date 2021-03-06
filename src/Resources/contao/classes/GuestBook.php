<?php

/*
 * Contao CMS (c) Leo Feyer
 *
 * @license LGPL-3.0-or-later
 */

// namespace Seefahrer\GuestBookBundle;

class GuestBook extends Module
{
    /** * Template * @var string */
    protected $strTemplate = 'mod_guestbook';
    /** * Display a wildcard in the back end * @return string */
    public function generate()
    {
        if (TL_MODE == 'BE')
        {
            $objTemplate = new BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### GUESTBOOK ENTRIES ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao?do=modules&act=edit&id=' . $this->id;
            return $objTemplate->parse();
        }
        return parent::generate();
    }
    /** * Generate module */
    protected function compile()
    {
        $limit = null;
        $arrComments = array();
        // Pagination
    if ($this->gb_perPage > 0)
        {
            $page = $this->Input->get('page') ? $this->Input->get('page') : 1;
            $limit = $this->gb_perPage;
            $offset = ($page - 1) * $this->gb_perPage;
            // Get total number of comments
            $objTotal = $this->Database->prepare("SELECT COUNT(*) AS count FROM tl_guestbook" . (!BE_USER_LOGGED_IN ? " WHERE published=1" : "")) ->execute($this->id);
            // Add pagination menu
            $objPagination = new Pagination($objTotal->count, $this->gb_perPage);
            $this->Template->pagination = $objPagination->generate("\n ");
        }
        // Get all published comments
        $gbEntriesStmt = $this->Database->prepare("SELECT * FROM tl_guestbook" . (!BE_USER_LOGGED_IN ? " WHERE published=1" : "") . " ORDER BY date" . (($this->gb_order == 'descending') ? " DESC" : ""));
        if ($limit)
        {
            $gbEntriesStmt->limit($limit, $offset);
        }
        $gbEntries = $gbEntriesStmt->execute($this->id);

        $total = $gbEntries->numRows;
        if ($total > 0)
        {
            $count = 0;
            $objTemplate = new FrontendTemplate($this->gb_template);
            while ($gbEntries->next())
            {   
                $objTemplate->name = $gbEntries->name;
                $objTemplate->place = $gbEntries->place;
                $objTemplate->email = $gbEntries->email;
                $objTemplate->website = $gbEntries->website;
                $objTemplate->titel = trim($gbEntries->titel);
                $objTemplate->message = trim($gbEntries->message);
                $objTemplate->comment = trim($gbEntries->comment);
                $objTemplate->datim = $this->parseDate($GLOBALS['TL_CONFIG']['datimFormat'], $gbEntries->tstamp);
                $objTemplate->date = $this->parseDate("d. M Y", $gbEntries->date);
                $objTemplate->class = (($count < 1) ? ' first' : '') . (($count >= ($total - 1)) ? ' last' : '') . (($count % 2 == 0) ? ' even' : ' odd');
                $objTemplate->id = 'c' . $gbEntries->id;
                $objTemplate->timestamp = $gbEntries->date;
                $arrComments[] = $objTemplate->parse();
                ++$count;
            }
        }
        $this->Template->comments = $arrComments;
    }
}
?>