<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\RootnavBundle\Model;

use Contao\Database;
use Contao\Model\Collection;
use Contao\PageModel;
use Contao\System;

/**
 * Class RootnavPageModel.
 *
 * @property string defineTarget
 * @property string pageTargets
 */
class RootnavPageModel extends PageModel
{
    /**
     * Find all published root pages by their IDs.
     *
     * @param int[] $ids     An array of page IDs
     * @param array $options An optional options array
     *
     * @return Collection|null A collection of models or null if there are no pages
     */
    public static function findPublishedRootPagesByIds(array $ids, array $options = [])
    {
        $framework = System::getContainer()->get('contao.framework');
        if (empty($ids)) {
            return null;
        }

        $t = static::$strTable;
        $columns = ["$t.id IN(".implode(',', array_map('intval', $ids)).") AND $t.type!='error_403' AND $t.type!='error_404'"];

        if (!BE_USER_LOGGED_IN) {
            $time = time();
            $columns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1";
        }

        if (!isset($options['order'])) {
            $options['order'] = $framework->createInstance(Database::class)->findInSet("$t.id", $ids);
        }

        return $framework->getAdapter(PageModel::class)->findBy($columns, null, $options);
    }
}
