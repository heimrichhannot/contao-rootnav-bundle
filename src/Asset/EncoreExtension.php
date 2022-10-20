<?php

/*
 * Copyright (c) 2022 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\RootnavBundle\Asset;

use HeimrichHannot\EncoreContracts\EncoreEntry;
use HeimrichHannot\RootnavBundle\HeimrichHannotContaoRootnavBundle;

class EncoreExtension implements \HeimrichHannot\EncoreContracts\EncoreExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function getBundle(): string
    {
        return HeimrichHannotContaoRootnavBundle::class;
    }

    /**
     * {@inheritDoc}
     */
    public function getEntries(): array
    {
        return [
            EncoreEntry::create('contao-rootnav-bundle', 'src/Resources/public/js/contao-rootnav-bundle.es6.js')
                ->setRequiresCss(false)
                ->setHead(false)
                ->addJsEntryToRemoveFromGlobals('huh_rootnav'),
        ];
    }
}
