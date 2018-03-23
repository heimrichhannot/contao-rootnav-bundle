<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\RootnavBundle\Test\Module;

use Contao\System;
use Contao\TestCase\ContaoTestCase;
use HeimrichHannot\RootnavBundle\Module\ModuleRootnav;

class ModuleRootnavTest extends ContaoTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $framework = $this->mockContaoFramework();
        $container = $this->mockContainer();
        $container->set('contao.framework', $framework);
        System::setContainer($container);
    }

    public function testCompile()
    {
        if (!defined('FE_USER_LOGGED_IN')) {
            define('FE_USER_LOGGED_IN', false);
        }

        $reflectionClass = new \ReflectionClass(ModuleRootnav::class);
        $testMethod = $reflectionClass->getMethod('compile');
        $testMethod->setAccessible(true);

        /** @var ModuleRootnav|\PHPUnit_Framework_MockObject_MockObject $module */
        $module = $this->getMockBuilder(ModuleRootnav::class)->disableOriginalConstructor()->setMethods([
            'skipTestFindPublishedRootPagesByIdsFrontend',
        ])->getMock();

        $result = $testMethod->invokeArgs($module, []);
        $this->assertNull($result);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function skiptestCompileLogin()
    {
        if (!defined('FE_USER_LOGGED_IN')) {
            define('FE_USER_LOGGED_IN', true);
        }

        $reflectionClass = new \ReflectionClass(ModuleRootnav::class);
        $testMethod = $reflectionClass->getMethod('compile');
        $testMethod->setAccessible(true);

        /** @var ModuleRootnav|\PHPUnit_Framework_MockObject_MockObject $module */
        $module = $this->getMockBuilder(ModuleRootnav::class)->disableOriginalConstructor()->setMethods([
            'skipTestFindPublishedRootPagesByIdsFrontend',
        ])->getMock();
    }
}
