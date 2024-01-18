<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 * @package rootnav
 * @author Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

use HeimrichHannot\RootnavBundle\Module\ModuleRootnav;

$dca = &$GLOBALS['TL_DCA']['tl_module'];

/**
 * Selectors
 */
array_insert($dca['palettes']['__selector__'], 0, 'defineTarget');

/**
 * Palettes
 */
$dca['palettes'][ModuleRootnav::NAME] = $dca['palettes']['customnav'];
$dca['palettes'][ModuleRootnav::NAME] = str_replace(
    'pages',
    'pages,defineTarget',
    $dca['palettes'][ModuleRootnav::NAME]
);

/**
 * Subpalettes
 */
$dca['subpalettes']['defineTarget'] = 'pageTargets';


/**
 * Fields
 */
$fields = [
    'defineTarget' => [
        'exclude' => true,
        'inputType' => 'checkbox',
        'eval' => ['submitOnChange' => true],
        'sql' => "char(1) NOT NULL default ''"
    ],
    'pageTargets' => [
        'inputType' => 'checkbox',
        'exclude' => true,
        'eval' => ['multiple' => true],
        'sql' => "blob NULL"
    ]
];

$dca['fields'] = array_merge($dca['fields'], $fields);