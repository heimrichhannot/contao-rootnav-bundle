<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\RootnavBundle\Test\EventListener;

use Contao\DataContainer;
use Contao\PageModel;
use Contao\TestCase\ContaoTestCase;
use HeimrichHannot\RootnavBundle\EventListener\CallbackListener;

class CallbackListenerTest extends ContaoTestCase
{
    protected $pageModelMethodCallCount = 0;

    protected function setUp()
    {
        parent::setUp();
        $this->pageModelMethodCallCount = 0;
    }

    public function testGetPage()
    {
        $dc = $this->getMockBuilder(DataContainer::class)->disableOriginalConstructor()->setMethods(['getPalette', 'save'])->getMock();
        $pageModelAdapter = $this->mockAdapter(['findMultipleByIds']);
        $pageModelAdapter->method('findMultipleByIds')->willReturnCallback(function ($ids, $options) {
            ++$this->pageModelMethodCallCount;
            if (\in_array(0, $ids, true)) {
                return null;
            }
            $collection = [];
            foreach ($ids as $id) {
                $collection[] = $this->mockClassWithProperties(PageModel::class, [
                    'id' => $id,
                    'title' => 'Seite',
                    'alias' => 'page',
                ]);
            }

            return $collection;
        });
        $framework = $this->mockContaoFramework([
            PageModel::class => $pageModelAdapter,
        ]);
        $listener = new CallbackListener($framework, '');

        $result = $listener->getPages($dc);
        $this->assertSame([], $result);

        $activeRecord = new \stdClass();
        $activeRecord->pages = [];
        $activeRecord->orderPages = [];
        $dc->activeRecord = $activeRecord;
        $result = $listener->getPages($dc);
        $this->assertSame([], $result);

        $activeRecord->pages = [0];
        $activeRecord->orderPages = [0];
        $dc->activeRecord = $activeRecord;
        $result = $listener->getPages($dc);
        $this->assertSame([], $result);
        $this->assertSame(1, $this->pageModelMethodCallCount);

        $this->pageModelMethodCallCount = 0;
        $activeRecord->pages = [1, 2];
        $activeRecord->orderPages = [1, 2];
        $dc->activeRecord = $activeRecord;
        $result = $listener->getPages($dc);
        $this->assertCount(2, $result);
        $this->assertSame(1, $this->pageModelMethodCallCount);
    }
}
