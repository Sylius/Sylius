<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\ImportExport;

use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use PhpSpec\ObjectBehavior;
use Sylius\Component\ImportExport\Model\ImportJobInterface;
use Sylius\Component\ImportExport\Model\ImportProfileInterface;
use Sylius\Component\ImportExport\Reader\ReaderInterface;
use Sylius\Component\ImportExport\Writer\WriterInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ImporterSpec extends ObjectBehavior
{
    function let(ServiceRegistryInterface $readerRegistry, ServiceRegistryInterface $writerRegistry, RepositoryInterface $importJobRepository, EntityManager $entityManager, Logger $logger)
    {
        $this->beConstructedWith($readerRegistry, $writerRegistry, $importJobRepository, $entityManager, $logger);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\ImportExport\Importer');
    }

    function it_implements_delegating_importer_interface()
    {
        $this->shouldImplement('Sylius\Component\ImportExport\ImporterInterface');
    }

    function it_imports_data_with_given_importer(
        $importJobRepository, 
        $logger,
        $readerRegistry,
        $writerRegistry,
        ImportJobInterface $importJob,
        ImportProfileInterface $importProfile,
        ReaderInterface $reader,
        WriterInterface $writer)
    {
        $importJobRepository->createNew()->willReturn($importJob);
        $importProfile->getId()->willReturn(1);

        $startTime = new \DateTime();
        $importJob->setStartTime($startTime)->shouldBeCalled()->willReturn($importJob);
        $importJob->setStatus('running')->shouldBeCalled()->willReturn($importJob);
        $importJob->setProfile($importProfile)->shouldBeCalled()->willReturn($importJob);

        $importJob->getId()->willReturn(1);
        $importJob->getStartTime()->willReturn($startTime);

        $logger->info(sprintf("Profile: 1; StartTime: %s", $startTime->format('Y-m-d H:i:s')))->shouldBeCalled();
        $importProfile->addJob($importJob)->shouldBeCalled();

        $importProfile->getReader()->willReturn('doctrine');
        $importProfile->getReaderConfiguration()->willReturn(array());
        $importProfile->getWriter()->willReturn('csv');
        $importProfile->getWriterConfiguration()->willReturn(array());

        $readerRegistry->get('doctrine')->willReturn($reader);
        $reader->setConfiguration(array())->shouldBeCalled();
        $reader->read()->willReturn(array(array('readData')));

        $writerRegistry->get('csv')->willReturn($writer);
        $writer->setConfiguration(array())->shouldBeCalled();

        $writer->write(array('readData'))->shouldBeCalled();

        $endTime = new \DateTime();
        $importJob->setUpdatedAt($endTime)->shouldBeCalled()->willReturn($importJob);
        $importJob->setEndTime($endTime)->shouldBeCalled()->willReturn($importJob);
        $importJob->setStatus('completed')->shouldBeCalled()->willReturn($importJob);
        $importJob->getEndTime()->shouldBeCalled()->willReturn($endTime);
        $logger->info(sprintf("Job: 1; EndTime: %s", $endTime->format('Y-m-d H:i:s')))->shouldBeCalled();

        $this->import($importProfile);
    }

    // function it_does_not_allow_to_import_data_without_reader_defined(
    //     $importJobRepository, 
    //     $logger,
    //     ImportJobInterface $importJob,
    //     ImportProfileInterface $importProfile)
    // {
    //     $importJobRepository->createNew()->willReturn($importJob);
    //     $importProfile->getId()->willReturn(1);

    //     $startTime = new \DateTime();
    //     $importJob->setStartTime($startTime)->shouldBeCalled()->willReturn($importJob);
    //     $importJob->setStatus('running')->shouldBeCalled()->willReturn($importJob);
    //     $importJob->setProfile($importProfile)->shouldBeCalled()->willReturn($importJob);

    //     $importJob->getId()->willReturn(1);
    //     $importJob->getStartTime()->willReturn($startTime);

    //     $logger->info(sprintf("Profile: 1; StartTime: %s", $startTime->format('Y-m-d H:i:s')))->shouldBeCalled();
    //     $importProfile->addJob($importJob)->shouldBeCalled();

    //     $logger->error(sprintf('ImportProfile: %d. Cannot read data with ImportProfile instance without reader defined.', $importProfile->getId()))->shouldBeCalled();

    //     $importProfile->getReader()->willReturn(null);
    //     $this->shouldThrow(new \InvalidArgumentException('Cannot read data with ImportProfile instance without reader defined.'))
    //     ->duringImport($importProfile);
    // }

    // function it_does_not_allow_to_import_data_without_writer_defined(ImportProfileInterface $importProfile)
    // {
    //     $importProfile->getReader()->willReturn('csv_reader');
    //     $importProfile->getWriter()->willReturn(null);
    //     $this->shouldThrow(new \InvalidArgumentException('Cannot write data with ImportProfile instance without writer defined.'))
    //     ->duringImport($importProfile);
    // }
}