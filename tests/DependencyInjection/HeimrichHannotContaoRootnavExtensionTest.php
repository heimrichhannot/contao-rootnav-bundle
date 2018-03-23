<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\RootnavBundle\Test\DependencyInjection;

use HeimrichHannot\RootnavBundle\DependencyInjection\HeimrichHannotContaoRootnavExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class HeimrichHannotContaoRootnavExtensionTest extends TestCase
{
    private $container;

    protected function setUp()
    {
        parent::setUp();
        $this->container = new ContainerBuilder(new ParameterBag(['kernel.debug' => false]));

        $extension = new HeimrichHannotContaoRootnavExtension();
        $extension->load([], $this->container);
    }

    public function testCanBeInstantiated()
    {
        $this->assertInstanceOf(
            HeimrichHannotContaoRootnavExtension::class,
            new HeimrichHannotContaoRootnavExtension()
        );
    }
}
