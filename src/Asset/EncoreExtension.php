<?php

namespace HeimrichHannot\RootnavBundle\Asset;

use HeimrichHannot\EncoreContracts\EncoreEntry;
use HeimrichHannot\EncoreContracts\EncoreExtensionInterface;
use HeimrichHannot\RootnavBundle\HeimrichHannotContaoRootnavBundle;

class EncoreExtension implements EncoreExtensionInterface
{
    /**
     * @inheritDoc
     */
    public function getBundle(): string
    {
        return HeimrichHannotContaoRootnavBundle::class;
    }

    /**
     * @inheritDoc
     */
    public function getEntries(): array
    {
        return [
            EncoreEntry::create('contao-rootnav-bundle', 'src/Resources/public/js/contao-rootnav-bundle.es6.js')
                ->setRequiresCss(false)
                ->setIsHeadScript(false)
                ->addJsEntryToRemoveFromGlobals('huh_rootnav')
        ];
    }
}