<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\RootnavBundle\Test\Model;

use Contao\Database;
use Contao\PageModel;
use Contao\System;
use Contao\TestCase\ContaoTestCase;
use HeimrichHannot\RootnavBundle\Model\RootnavPageModel;

class RootnavPageModelTest extends ContaoTestCase
{
    protected function setUp()
    {
        parent::setUp();
        if (!\defined('BE_USER_LOGGED_IN')) {
            \define('BE_USER_LOGGED_IN', false);
        }
        $pageModelAdapter = $this->mockAdapter(['findBy']);
        $pageModelAdapter->method('findBy')->willReturnCallback(function ($col = [], $val = [], $opt = []) {
            return [$col, $val, $opt];
        });
        $framework = $this->mockContaoFramework([
            PageModel::class => $pageModelAdapter,
        ]);
        $framework->method('createInstance')->willReturnCallback(function ($class) {
            $databaseMock = $this->mockAdapter(['findInSet']);
            $databaseMock->method('findInSet');
            switch ($class) {
                case Database::class:
                    return $databaseMock;
            }
        });
        $container = $this->mockContainer();
        $container->set('contao.framework', $framework);
        System::setContainer($container);
    }

    public function testFindPublishedRootPagesByIds()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|RootnavPageModel $mock */
        $mock = $this->getMockBuilder(RootnavPageModel::class)->setMethods(null)->disableOriginalConstructor()
            ->getMock();

        $result = $mock->findPublishedRootPagesByIds([]);
        $this->assertNull($result);

        $result = $mock->findPublishedRootPagesByIds([1, 2]);
        $this->assertCount(2, $result[0]);
        $this->assertNull($result[1]);
        $this->assertCount(1, $result[2]);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function skipTestFindPublishedRootPagesByIdsFrontend()
    {
        if (!\defined('BE_USER_LOGGED_IN')) {
            \define('BE_USER_LOGGED_IN', true);
        }
        /** @var \PHPUnit_Framework_MockObject_MockObject|RootnavPageModel $mock */
        $mock = $this->getMockBuilder(RootnavPageModel::class)->setMethods(null)->disableOriginalConstructor()
            ->getMock();

        $result = $mock->findPublishedRootPagesByIds([]);
        $this->assertNull($result);

        $result = $mock->findPublishedRootPagesByIds([1, 2]);
        $this->assertCount(1, $result[0]);
        $this->assertNull($result[1]);
        $this->assertCount(1, $result[2]);
    }
}
