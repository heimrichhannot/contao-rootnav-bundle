<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\RootnavBundle\EventListener;

use Contao\Config;
use Contao\DataContainer;
use Contao\PageModel;

class CallbackListener
{
    /**
     * Get all pages from pages field as array.
     *
     * @param DataContainer $dc
     *
     * @return array The pages
     */
    public function getPages(DataContainer $dc)
    {
        $arrOptions = [];

        if (!$dc->activeRecord && !is_array($dc->activeRecord->pages)) {
            return $arrOptions;
        }

        $arrPages = deserialize($dc->activeRecord->pages, true);
        $arrOrder = deserialize($dc->activeRecord->orderPages, true);

        $objPages = PageModel::findMultipleByIds($arrPages, ['order' => 'FIELD(id,'.implode(',', $arrOrder).')']);

        if (null === $objPages) {
            return $arrOptions;
        }

        while ($objPages->next()) {
            $arrOptions[$objPages->id] = $arrValues[$objPages->id] = $objPages->title.' ('.$objPages->alias.Config::get('urlSuffix').')';
        }

        return $arrOptions;
    }
}
