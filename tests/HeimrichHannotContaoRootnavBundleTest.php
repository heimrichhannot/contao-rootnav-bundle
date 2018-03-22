<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
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
