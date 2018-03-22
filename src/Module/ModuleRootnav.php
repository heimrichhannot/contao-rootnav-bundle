<?php
/**
 * Contao Open Source CMS
 * 
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 * @package rootnav
 * @author Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\RootnavBundle\Module;


use Contao\Controller;
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
	 * Generate the module
	 */
	protected function compile()
	{
	    $container = System::getContainer();
	    $framework = $container->get('contao.framework');

		global $objPage;

		$groups = array();
		// Get all groups of the current front end user
		if (FE_USER_LOGGED_IN)
		{
		    /** @var FrontendUser $user */
		    $user = $framework->createInstance(FrontendUser::class);
			$groups = $user->groups;
		}

		// Get all active pages
		$rootPages = RootnavPageModel::findPublishedRootPagesByIds($this->pages);

		// Return if there are no pages
		if (!$rootPages)
		{
			return;
		}

		$arrPages = array();

		// Sort the array keys according to the given order
		if ($this->orderPages != '')
		{
			$tmp = deserialize($this->orderPages);

			if (!empty($tmp) && is_array($tmp))
			{
				$arrPages = array_map(function(){}, array_flip($tmp));
			}
		}

		// Add the items to the pre-sorted array
		while ($rootPages->next())
		{
			$arrPages[$rootPages->id] = $rootPages->current()->loadDetails()->row(); // see #3765
		}

		// Set default template
		if ($this->navigationTpl == '')
		{
			$this->navigationTpl = 'nav_default';
		}

		$objTemplate = $framework->createInstance(FrontendTemplate::class, [$this->navigationTpl]);

		$objTemplate->type = get_class($this);
		$objTemplate->cssID = $this->cssID; // see #4897 and 6129
		$objTemplate->level = 'level_1';

		$arrTargetPages = StringUtil::deserialize($this->pageTargets, true);

		$items = $this->generateNavigationItems($arrPages, $groups, $arrTargetPages);

		// Add classes first and last
		$items[0]['class'] = trim($items[0]['class'] . ' first');
		$last = count($items) - 1;
		$items[$last]['class'] = trim($items[$last]['class'] . ' last');

		$objTemplate->items = $items;

		$this->Template->request = $framework->getAdapter(Environment::class)->get('indexFreeRequest');
		$this->Template->skipId = 'skipNavigation' . $this->id;
		$this->Template->skipNavigation = StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['skipNavigation']);
		$this->Template->items = !empty($items) ? $objTemplate->parse() : '';
	}

	protected function generateNavigationItems($arrPages, $groups, $arrTargetPages)
    {
        $framework = System::getContainer()->get('contao.framework');
        foreach ($arrPages as $arrPage)
        {
            // Skip hidden pages (see #5832)
            if (!is_array($arrPage))
            {
                continue;
            }

            $_groups = StringUtil::deserialize($arrPage['groups']);

            // Do not show protected pages unless a back end or front end user is logged in
            if (!$arrPage['protected'] || BE_USER_LOGGED_IN || (is_array($_groups) && count(array_intersect($_groups, $groups))) || $this->showProtected)
            {

                // Get href
                $href = $framework->getAdapter(Controller::class)->generateFrontendUrl($arrPage, null, $arrPage['rootLanguage'], true);


                // Remove root page alias from href
                if($arrPage['type'] == 'root')
                {
                    $arrHref = parse_url($href);
                    $arrHref['path'] = str_replace($arrPage['alias'], '', $arrHref['path']);

                    if(substr($arrHref['path'], 0, 1) !== '/')
                    {
                        $arrHref['path'] = '/' . $arrHref['path'];
                    }

                    // build url without root page alias
                    $href = '';
                    $href .= isset($arrHref['scheme']) ? $arrHref['scheme'] . '://' : '';
                    $href .= isset($arrHref['host']) ? $arrHref['host'] : '';
                    $href .= isset($arrHref['port']) ? ':' . $arrHref['port'] : '';
                    $href .= isset($arrHref['path']) ? $arrHref['path'] : '';
                }

                $trail = in_array($arrPage['id'], $objPage->trail);

                $strClass = trim($arrPage['cssClass'] . ($trail ? ' trail' : ''));
                $row = $arrPage;

                $row['isActive'] = false;
                $row['isTrail'] = $trail;
                $row['class'] = $strClass;
                $row['title'] = specialchars($arrPage['title'], true);
                $row['pageTitle'] = specialchars($arrPage['pageTitle'], true);
                $row['link'] = $arrPage['title'];
                $row['href'] = $href;
                $row['nofollow'] = (strncmp($arrPage['robots'], 'noindex', 7) === 0);
                $row['target'] = '';
                $row['description'] = str_replace(array("\n", "\r"), array(' ' , ''), $arrPage['description']);

                $defineTarget = $arrPage['type'] == 'redirect' && $arrPage['target'];
                $defineTarget = $this->defineTarget && in_array($arrPage['id'], $arrTargetPages);

                // Override the link target
                if ($defineTarget)
                {
                    $row['target'] = ($objPage->outputFormat == 'xhtml') ? ' onclick="return !window.open(this.href)"' : ' target="_blank"';
                }

                $items[] = $row;
            }
        }
    }
} 