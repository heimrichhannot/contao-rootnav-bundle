<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\RootnavBundle\Module;

use Contao\Environment;
use Contao\FrontendTemplate;
use Contao\FrontendUser;
use Contao\ModuleCustomnav;
use Contao\StringUtil;
use Contao\System;
use HeimrichHannot\RootnavBundle\Model\RootnavPageModel;

class ModuleRootnav extends ModuleCustomnav
{
    const NAME = 'huh_rootnav_module';

    /**
     * Generate the module.
     */
    protected function compile()
    {
        $container = System::getContainer();
        $framework = $container->get('contao.framework');

        $groups = [];
        // Get all groups of the current front end user
        if (FE_USER_LOGGED_IN) {
            /** @var FrontendUser $user */
            $user = $framework->createInstance(FrontendUser::class);
            $groups = $user->groups;
        }

        if (!$this->pages || empty($this->pages)) {
            return;
        }

        // Get all active pages
        /** @var RootnavPageModel $rootPages */
        $rootPages = $framework->getAdapter(RootnavPageModel::class)->findPublishedRootPagesByIds($this->pages);

        // Return if there are no pages
        if (!$rootPages) {
            return;
        }

        $pages = [];

        // Sort the array keys according to the given order
        if ($this->orderPages) {
            $tmp = StringUtil::deserialize($this->orderPages);

            if (!empty($tmp) && is_array($tmp)) {
                $pages = array_map(function () {
                }, array_flip($tmp));
            }
        }

        // Add the items to the pre-sorted array
        foreach ($rootPages as $rootPage) {
            $pages[$rootPage->id] = $rootPage->loadDetails()->row(); // see #3765
        }

        // Set default template
        if (empty($this->navigationTpl)) {
            $this->navigationTpl = 'nav_default';
        }

        $objTemplate = $framework->createInstance(FrontendTemplate::class, [$this->navigationTpl]);

        $objTemplate->type = get_class($this);
        $objTemplate->cssID = $this->cssID; // see #4897 and 6129
        $objTemplate->level = 'level_1';

        $arrTargetPages = StringUtil::deserialize($this->pageTargets, true);

        $items = $this->generateNavigationItems($pages, $groups, $arrTargetPages);

        // Add classes first and last
        $items[0]['class'] = trim($items[0]['class'].' first');
        $last = count($items) - 1;
        $items[$last]['class'] = trim($items[$last]['class'].' last');

        $objTemplate->items = $items;

        $this->Template->request = $framework->getAdapter(Environment::class)->get('indexFreeRequest');
        $this->Template->skipId = 'skipNavigation'.$this->id;
        $this->Template->skipNavigation = StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['skipNavigation']);
        $this->Template->items = !empty($items) ? $objTemplate->parse() : '';
    }

    /**
     * @param array $pages
     * @param array $groups
     * @param array $targetPages
     *
     * @return array
     */
    protected function generateNavigationItems(array $pages, $groups, $targetPages)
    {
        global $objPage;
        $framework = System::getContainer()->get('contao.framework');
        $router = System::getContainer()->get('contao.routing.url_generator');

        $items = [];

        foreach ($pages as $page) {
            // Skip hidden pages (see #5832)
            if (!is_array($page)) {
                continue;
            }

            $pageGroups = StringUtil::deserialize($page['groups']);

            // Do not show protected pages unless a back end or front end user is logged in
            if (!$page['protected'] || BE_USER_LOGGED_IN || (is_array($pageGroups) && count(array_intersect($pageGroups, $groups))) || $this->showProtected) {
                // Get href
                $href = $router->generate($page['alias']);

                // Remove root page alias from href
                if ('root' === $page['type']) {
                    $arrHref = parse_url($href);
                    $arrHref['path'] = str_replace($page['alias'], '', $arrHref['path']);

                    if ('/' !== substr($arrHref['path'], 0, 1)) {
                        $arrHref['path'] = '/'.$arrHref['path'];
                    }

                    // build url without root page alias
                    $href = '';
                    $href .= isset($arrHref['scheme']) ? $arrHref['scheme'].'://' : '';
                    $href .= isset($arrHref['host']) ? $arrHref['host'] : '';
                    $href .= isset($arrHref['port']) ? ':'.$arrHref['port'] : '';
                    $href .= isset($arrHref['path']) ? $arrHref['path'] : '';
                }

                $trail = in_array($page['id'], $objPage->trail, true);

                $strClass = trim($page['cssClass'].($trail ? ' trail' : ''));
                $row = $page;

                $row['isActive'] = false;
                $row['isTrail'] = $trail;
                $row['class'] = $strClass;
                $row['title'] = StringUtil::specialchars($page['title'], true);
                $row['pageTitle'] = StringUtil::specialchars($page['pageTitle'], true);
                $row['link'] = $page['title'];
                $row['href'] = $href;
                $row['nofollow'] = (0 === strncmp($page['robots'], 'noindex', 7));
                $row['target'] = '';
                $row['description'] = str_replace(["\n", "\r"], [' ', ''], $page['description']);

                $defineTarget = $this->defineTarget && in_array($page['id'], $targetPages, true);

                // Override the link target
                if ($defineTarget) {
                    $row['target'] = ('xhtml' === $objPage->outputFormat) ? ' onclick="return !window.open(this.href)"' : ' target="_blank"';
                }

                $items[] = $row;
            }
        }

        return $items;
    }
}
