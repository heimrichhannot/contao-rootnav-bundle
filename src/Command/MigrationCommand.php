<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\RootnavBundle\Command;

use Contao\CoreBundle\Command\AbstractLockedCommand;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\ModuleModel;
use HeimrichHannot\RootnavBundle\Module\ModuleRootnav;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MigrationCommand extends AbstractLockedCommand
{
    /**
     * @var bool
     */
    protected $dryRun = false;
    /**
     * @var ContaoFramework
     */
    private $framework;

    public function __construct(ContaoFramework $framework)
    {
        parent::__construct();
        $this->framework = $framework;
    }

    protected function configure()
    {
        $this->setName('huh:rootnav:migration')
            ->setDescription('Migration from rootnav module to bundle')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Performs a run without making changes to the database.')
        ;
    }

    /**
     * Executes the command.
     *
     * @return int|null
     */
    protected function executeLocked(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $this->framework->initialize();
        $io->title('Start rootnav migration');
        if ($input->hasOption('dry-run') && $input->getOption('dry-run')) {
            $this->dryRun = true;
            $io->note('Dry run enabled, no data will be changed.');
            $io->newLine();
        }

        $rootnavModules = ModuleModel::findByType('rootnav');

        if (!$rootnavModules) {
            $io->success('Found no rootnav modules. Migration finished.');

            return 0;
        }

        $io->text('Found <fg=yellow>'.$rootnavModules->count().'</> rootnav frontend modules.');
        $io->newLine();

        $io->progressStart($rootnavModules->count());
        foreach ($rootnavModules as $module) {
            $io->progressAdvance();
            $module->type = ModuleRootnav::NAME;
            if (!$this->dryRun) {
                $module->save();
            }
        }
        $io->progressFinish();
        $io->success('Finished rootnav migration command');
    }
}
