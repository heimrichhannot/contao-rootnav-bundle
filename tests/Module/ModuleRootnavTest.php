<?php

/*
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\RootnavBundle\Test\Module;

use Contao\Environment;
use Contao\FrontendTemplate;
use Contao\FrontendUser;
use Contao\System;
use Contao\TestCase\ContaoTestCase;
use HeimrichHannot\RootnavBundle\Model\RootnavPageModel;
use HeimrichHannot\RootnavBundle\Module\ModuleRootnav;

class ModuleRootnavTest extends ContaoTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $routerMock = $this->mockAdapter(['generate']);
        $routerMock->method('generate')->willReturnOnConsecutiveCalls('/home.html', 'home.html');

        $container = $this->mockContainer();
        $container->set('contao.framework', $this->createFrameworkMock());
        $container->set('contao.routing.url_generator', $routerMock);
        System::setContainer($container);
    }

    public function createFrameworkMock()
    {
        $rootNavPageModelAdapter = $this->mockAdapter([
            'findPublishedRootPagesByIds',
            'loadDetails',
        ]);
        $rootNavPageModelAdapter->method('findPublishedRootPagesByIds')->willReturnCallback(function ($pagesIds) {
            $pages = [];
            foreach ($pagesIds as $pageId) {
                $page = $this->mockAdapter(['row', 'loadDetails']);
                $page->method('loadDetails')->willReturnSelf();
                $page->method('row')->willReturn(['id' => $pageId]);
                $page->id = $pageId;
                switch ($pageId) {
                    case 4:
                        $pages[] = $page;
                        // no break
                    case 5:
                        $pages[] = $page;
                }
            }
            if (empty($pages)) {
                return null;
            }

            return $pages;
        });

        $environmentMock = $this->mockAdapter(['get']);
        $environmentMock->method('get')->willReturnCallback(function ($param) {
            switch ($param) {
                case 'indexFreeRequest':
                    return 'home.html';
            }
        });

        $framework = $this->mockContaoFramework([
            RootnavPageModel::class => $rootNavPageModelAdapter,
            Environment::class => $environmentMock,
        ]);
        $framework->method('createInstance')->willReturnCallback(function ($class) {
            switch ($class) {
                case FrontendTemplate::class:
                    $template = $this->createMock(FrontendTemplate::class);
                    $template->method('parse')->willReturn('Parseresult');

                    return $template;
                case FrontendUser::class:
                    $frontenduser = $this->createMock(FrontendUser::class)->method('__get');
                    $frontenduser->groups = ['group1'];

                    return $frontenduser;
                default:
                    return null;
            }
        });

        return $framework;
    }

    public function testCompile()
    {
        if (!\defined('FE_USER_LOGGED_IN')) {
            \define('FE_USER_LOGGED_IN', false);
        }

        $reflectionClass = new \ReflectionClass(ModuleRootnav::class);
        $testMethod = $reflectionClass->getMethod('compile');
        $testMethod->setAccessible(true);

        /** @var ModuleRootnav|\PHPUnit_Framework_MockObject_MockObject $module */
        $module = $this->getMockBuilder(ModuleRootnav::class)->disableOriginalConstructor()->setMethods([
            'compile',
            'generateNavigationItems',
        ])->getMock();
        $GLOBALS['TL_LANG']['MSC']['skipNavigation'] = 'Navigation überspringen';

        $result = $testMethod->invokeArgs($module, []);
        $this->assertNull($result);

        $module->pages = [1, 2];
        $result = $testMethod->invokeArgs($module, []);
        $this->assertNull($result);

        $module->expects($this->once())->method('generateNavigationItems')->willReturnCallback(function ($arrPages, $groups, $arrTargetPages) {
            $this->assertSame([4, 5], array_keys($arrPages));

            return [
                ['id' => 4, 'class' => ''],
                ['id' => 5, 'class' => ''],
            ];
        });
        $module->pages = [4, 5];
        $module->Template = new \stdClass();
        $result = $testMethod->invokeArgs($module, []);
        $this->assertNull($result);
        $this->assertSame('Parseresult', $module->Template->items);

        $module = $this->getMockBuilder(ModuleRootnav::class)->disableOriginalConstructor()->setMethods([
            'compile',
            'generateNavigationItems',
        ])->getMock();
        $module->expects($this->once())->method('generateNavigationItems')->willReturnCallback(function ($arrPages, $groups, $arrTargetPages) {
            $this->assertSame([5, 4], array_keys($arrPages));

            return [
                ['id' => 5, 'class' => ''],
                ['id' => 4, 'class' => ''],
            ];
        });
        $module->pages = [4, 5];
        $module->Template = new \stdClass();
        $module->orderPages = serialize([5, 4]);
        $result = $testMethod->invokeArgs($module, []);
        $this->assertNull($result);
        $this->assertSame('Parseresult', $module->Template->items);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testCompileLogin()
    {
        if (!\defined('FE_USER_LOGGED_IN')) {
            \define('FE_USER_LOGGED_IN', true);
        }

        $reflectionClass = new \ReflectionClass(ModuleRootnav::class);
        $testMethod = $reflectionClass->getMethod('compile');
        $testMethod->setAccessible(true);
        $GLOBALS['TL_LANG']['MSC']['skipNavigation'] = 'Navigation überspringen';

        /** @var ModuleRootnav|\PHPUnit_Framework_MockObject_MockObject $module */
        $module = $this->getMockBuilder(ModuleRootnav::class)->disableOriginalConstructor()->setMethods([
            'skipTestFindPublishedRootPagesByIdsFrontend',
            'generateNavigationItems',
        ])->getMock();
        $module->expects($this->once())->method('generateNavigationItems')->willReturnCallback(function ($arrPages, $groups, $arrTargetPages) {
            $this->assertSame(['group1'], $groups);

            return [
                ['id' => 4, 'class' => ''],
                ['id' => 5, 'class' => ''],
            ];
        });

        $module->pages = [4, 5];
        $module->Template = new \stdClass();
        $result = $testMethod->invokeArgs($module, []);
        $this->assertNull($result);
        $this->assertSame('Parseresult', $module->Template->items);
    }

    public function testGenerateNavigationItems()
    {
        if (!\defined('BE_USER_LOGGED_IN')) {
            \define('BE_USER_LOGGED_IN', false);
        }

        $reflectionClass = new \ReflectionClass(ModuleRootnav::class);
        $testMethod = $reflectionClass->getMethod('generateNavigationItems');
        $testMethod->setAccessible(true);

        $objPage = new \stdClass();
        $objPage->trail = [2];
        $objPage->outputFormat = 'xhtml';

        $GLOBALS['objPage'] = $objPage;

        /** @var ModuleRootnav|\PHPUnit_Framework_MockObject_MockObject $module */
        $module = $this->getMockBuilder(ModuleRootnav::class)->disableOriginalConstructor()->setMethods([
            'skipTestFindPublishedRootPagesByIdsFrontend',
            'generateNavigationItems',
        ])->getMock();

        $result = $testMethod->invokeArgs($module, [
            ['a', 'b', 'c'],
            [],
            [],
        ]);
        $this->assertEmpty($result);

        $result = $testMethod->invokeArgs($module, [
            [['groups' => serialize([1, 2, 3]), 'protected' => true], ['groups' => serialize([4, 5, 6]), 'protected' => true]],
            [],
            [],
        ]);
        $this->assertEmpty($result);

        $result = $testMethod->invokeArgs($module, [
            [
                [
                    'id' => 1,
                    'alias' => 'example-org',
                    'type' => 'root',
                    'pageTitle' => 'example.org',
                    'title' => 'example',
                    'description' => '',
                    'groups' => serialize([1, 2, 3]),
                    'protected' => false,
                    'cssClass' => '',
                    'robots' => 'nofollow',
                    'dns' => 'example.org',
                ],
                [
                    'id' => 2,
                    'alias' => 'index',
                    'type' => 'regular',
                    'pageTitle' => 'Home',
                    'title' => 'example Home',
                    'description' => '',
                    'groups' => serialize([4, 5, 6]),
                    'protected' => false,
                    'cssClass' => '',
                    'robots' => '',
                    'dns' => 'example.org',
                ],
            ],
            [],
            [],
        ]);
        $this->assertCount(2, $result);

        $module->defineTarget = true;
        $result = $testMethod->invokeArgs($module, [
            [
                [
                    'id' => 1,
                    'alias' => 'example-org',
                    'type' => 'root',
                    'pageTitle' => 'example.org',
                    'title' => 'example',
                    'description' => '',
                    'groups' => serialize([1, 2, 3]),
                    'protected' => false,
                    'cssClass' => '',
                    'robots' => 'nofollow',
                    'dns' => 'example.org',
                ],
                [
                    'id' => 2,
                    'alias' => 'index',
                    'type' => 'regular',
                    'pageTitle' => 'Home',
                    'title' => 'example Home',
                    'description' => '',
                    'groups' => serialize([4, 5, 6]),
                    'protected' => false,
                    'cssClass' => '',
                    'robots' => '',
                ],
            ],
            [],
            [1],
        ]);
        $this->assertCount(2, $result);
    }
}
