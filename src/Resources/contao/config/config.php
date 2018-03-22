<?php

/**
 * Front end modules
 */
$GLOBALS['FE_MOD']['navigationMenu'][\HeimrichHannot\RootnavBundle\Module\ModuleRootnav::NAME] = \HeimrichHannot\RootnavBundle\Module\ModuleRootnav::class;

/**
 * Javascript
 */
if (TL_MODE == 'FE') {
	$GLOBALS['TL_JAVASCRIPT']['huh_rootnav'] = '/bundles/heimrichhannotcontaorootnav/js/jquery.rootnav.js|static';
}
