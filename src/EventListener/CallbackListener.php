<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\RootnavBundle\EventListener;

use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\DataContainer;
use Contao\Model\Collection;
use Contao\PageModel;
use Contao\StringUtil;

class CallbackListener
{
    /**
     * @var ContaoFrameworkInterface
     */
    private $framework;
    /**
     * @var string
     */
    private $urlSuffix;

    public function __construct(ContaoFrameworkInterface $framework, string $urlSuffix)
    {
        $this->framework = $framework;
        $this->urlSuffix = $urlSuffix;
    }

    /**
     * Get all pages from pages field as array.
     *
     * @return array The pages
     */
    public function getPages(DataContainer $dc)
    {
        $options = [];

        if (!$dc->activeRecord) {
            return $options;
        }

        $pagesData = StringUtil::deserialize($dc->activeRecord->pages, true);
        $order = StringUtil::deserialize($dc->activeRecord->orderPages, true);

        if (empty($pagesData)) {
            return $options;
        }

        /** @var PageModel|Collection $pages */
        $pages = $this->framework->getAdapter(PageModel::class)->findMultipleByIds($pagesData, ['order' => 'FIELD(id,'.implode(',', $order).')']);

        if (!$pages) {
            return $options;
        }

        foreach ($pages as $page) {
            $options[$page->id] = $page->title.' ('.$page->alias.($this->urlSuffix ? '.'.$this->urlSuffix : '').')';
        }

        return $options;
    }
}
