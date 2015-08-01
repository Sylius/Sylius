<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ImportExportBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ImportDataCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sylius:import')
            ->setDescription('Command for importing data based on given import profile.')
            ->addArgument(
                'code',
                InputArgument::REQUIRED,
                'Code of import profile.'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $importProfile = $this->getContainer()->get('sylius.repository.import_profile')->findOneBy(
            array(
                'code' => $input->getArgument('code'),
            ));

        if ($importProfile === null) {
            throw new \InvalidArgumentException('There is no import profile with given code.');
        }

        $logger = $this->getContainer()->get('logger');
        $streamHandlerFactory = $this->getContainer()->get('sylius.import.stream_handler.factory');
        $logger->pushHandler($streamHandlerFactory->create('import_profile_'.$importProfile->getId()));

        $this->getContainer()->get('sylius.import_export.importer')->import($importProfile, $logger);

        $output->write('Command executed successfully!');
    }
}
