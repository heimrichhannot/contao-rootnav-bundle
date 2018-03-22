<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\RootnavBundle\EventListener;


use Contao\Config;
use Contao\DataContainer;
use Contao\PageModel;

class CallbackListener
{
    /**
     * Get all pages from pages field as array
     * @param DataContainer $dc
     *
     * @return array The pages
     */
    public function getPages(DataContainer $dc)
    {
        $arrOptions = array();

        if (!$dc->activeRecord && !is_array($dc->activeRecord->pages))
        {
            return $arrOptions;
        }

        $arrPages = deserialize($dc->activeRecord->pages, true);
        $arrOrder = deserialize($dc->activeRecord->orderPages, true);

        $objPages = PageModel::findMultipleByIds($arrPages, array('order' => 'FIELD(id,' . implode(",", $arrOrder) . ')'));

        if($objPages === null)
        {
            return $arrOptions;
        }

        while($objPages->next())
        {
            $arrOptions[$objPages->id] = $arrValues[$objPages->id] = $objPages->title . ' (' . $objPages->alias . Config::get('urlSuffix') . ')';
        }

        return $arrOptions;
    }
}