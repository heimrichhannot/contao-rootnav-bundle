<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\RootnavBundle\Test;


use HeimrichHannot\RootnavBundle\HeimrichHannotContaoRootnavBundle;
use PHPUnit\Framework\TestCase;

class HeimrichHannotContaoRootnavBundleTest extends TestCase
{
    public function testCanBeInstantiated()
    {
        $bundle = new HeimrichHannotContaoRootnavBundle();
        $this->assertInstanceOf(HeimrichHannotContaoRootnavBundle::class, $bundle);
    }
}