<?php
/**
 * Contao Open Source CMS
 * 
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 * @package rootnav
 * @author Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

$dc = &$GLOBALS['TL_DCA']['tl_module'];

/**
 * Selectors
 */
array_insert($dc['palettes']['__selector__'], 0 , 'defineTarget');

/**
 * Palettes
 */
$dc['palettes']['rootnav'] = $dc['palettes']['customnav'];
$dc['palettes']['rootnav'] = str_replace('pages', ',pages,defineTarget', $dc['palettes']['rootnav']);

/**
 * Subpalettes
 */
$dc['subpalettes']['defineTarget'] = 'pageTargets';


/**
 * Fields
 */
$fields = [
    'defineTarget' =>
        [
            'label'     => &$GLOBALS['TL_LANG']['tl_module']['defineTarget'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default ''"
        ],
    'pageTargets'  =>
        [
            'label'            => &$GLOBALS['TL_LANG']['tl_module']['pageTargets'],
            'inputType'        => 'checkbox',
            'exclude'          => true,
            'options_callback' => ['huh.rootnav.listener.callback', 'getPages'],
            'eval'             => ['multiple' => true],
            'sql'              => "blob NULL"
        ]
];

$dc['fields'] = array_merge($dc['fields'], $fields);