<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\RootnavBundle\EventListener;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Contao\Model\Collection;
use Contao\PageModel;
use Contao\StringUtil;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CallbackListener
{
    private ContaoFramework $framework;
    private ParameterBagInterface $parameterBag;

    public function __construct(ContaoFramework $framework, ParameterBagInterface $parameterBag)
    {
        $this->framework = $framework;
        $this->parameterBag = $parameterBag;
    }

    /**
     * Get all pages from pages field as array.
     *
     * @return array The pages
     *
     * @Callback(table="tl_module", target="fields.pageTargets.options")
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
        $pages = $this->framework->getAdapter(PageModel::class)->findMultipleByIds(
            $pagesData,
            empty($order) ? [] : ['order' => 'FIELD(id,' . implode(',', $order) . ')']
        );

        if (!$pages) {
            return $options;
        }

        $urlSuffix = $this->parameterBag->get('contao.url_suffix') ? '.'.$this->parameterBag->get('contao.url_suffix') : '';

        foreach ($pages as $page) {
            $options[$page->id] = $page->title.' ('.$page->alias.$urlSuffix.')';
        }

        return $options;
    }
}
