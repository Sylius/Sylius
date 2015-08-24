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
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Sylius\Component\ImportExport\Converter\DateConverter;
use Sylius\Component\ImportExport\Logger\ImportExportLogger;
use Sylius\Component\ImportExport\Model\ExportJobInterface;
use Sylius\Component\ImportExport\Model\ExportProfileInterface;
use Sylius\Component\ImportExport\Provider\CurrentDateProviderInterface;
use Sylius\Component\ImportExport\Reader\ReaderInterface;
use Sylius\Component\ImportExport\Writer\WriterInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ExporterSpec extends ObjectBehavior
{
    function let(
        CurrentDateProviderInterface $dateProvider,
        DateConverter $dateConverter,
        EntityManager $entityManager,
        RepositoryInterface $exportJobRepository,
        ServiceRegistryInterface $readerRegistry,
        ServiceRegistryInterface $writerRegistry
    ) {
        $this->beConstructedWith(
            $dateProvider,
            $dateConverter,
            $entityManager,
            $exportJobRepository,
            $readerRegistry,
            $writerRegistry
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\ImportExport\Exporter');
    }

    function it_implements_importer_interface() {
        $this->shouldImplement('Sylius\Component\ImportExport\ExporterInterface');
    }

    function it_extends_abstract_job_runner()
    {
        $this->shouldHaveType('Sylius\Component\ImportExport\JobRunner');
    }

    function it_implements_job_runner_interface() {
        $this->shouldImplement('Sylius\Component\ImportExport\JobRunnerInterface');
    }

    function it_implements_delegating_exporter_interface()
    {
        $this->shouldImplement('Sylius\Component\ImportExport\ExporterInterface');
    }

     function it_exports_data_with_given_exporter(
         $dateProvider,
         $dateConverter,
         $exportJobRepository,
         $entityManager,
         $readerRegistry,
         $writerRegistry,
         \DateTime $dateTime,
         ExportJobInterface $exportJob,
         ExportProfileInterface $exportProfile,
         LoggerInterface $logger,
         ReaderInterface $reader,
         WriterInterface $writer
     ) {
         $dateProvider->getCurrentDate()->willReturn($dateTime);
         $dateConverter->toString($dateTime, 'Y-m-d H:i:s')->willReturn('2015-06-29 12:40:00', '2015-06-29 13:40:00');

         $exportJobRepository->createNew()->willReturn($exportJob);
         $exportJob->getId()->willReturn(2);
         $exportJob->setStartTime($dateTime)->shouldBeCalled();
         $exportJob->getStartTime()->willReturn($dateTime);
         $exportJob->setStatus(Argument::type('string'))->shouldBeCalledTimes(2);
         $exportJob->setProfile($exportProfile)->shouldBeCalled();
         $exportJob->setEndTime($dateTime)->shouldBeCalled();
         $exportJob->getEndTime()->willReturn($dateTime);

         $logger->info("Job: 2; EndTime: 2015-06-29 13:40:00")->shouldBeCalled();
         $logger->info("Profile: 1; StartTime: 2015-06-29 12:40:00")->shouldBeCalled();

         $entityManager->persist(Argument::type('Sylius\Component\ImportExport\Model\ExportJobInterface'))->shouldBeCalledTimes(2);
         $entityManager->persist(Argument::type('Sylius\Component\ImportExport\Model\ExportProfileInterface'))->shouldBeCalled();
         $entityManager->flush()->shouldBeCalledTimes(2);

         $exportProfile->getId()->willReturn(1);
         $exportProfile->getReader()->willReturn('doctrine');
         $exportProfile->getReaderConfiguration()->willReturn(array());
         $exportProfile->getWriter()->willReturn('csv');
         $exportProfile->getWriterConfiguration()->willReturn(array());
         $exportProfile->addJob($exportJob)->shouldBeCalled();

         $readerRegistry->get('doctrine')->willReturn($reader);
         $reader->read(array(), $logger)->willReturn(array('readData1'),array('readData2'), null);
         $reader->finalize($exportJob)->shouldBeCalled();
         $reader->getResultCode()->willReturn(0);

         $writerRegistry->get('csv')->willReturn($writer);
         $writer->write(array('readData1'), array(), $logger)->shouldBeCalled();
         $writer->write(array('readData2'), array(), $logger)->shouldBeCalled();
         $writer->finalize($exportJob, array())->shouldBeCalled();
         $writer->getResultCode()->willReturn(0);

         $this->export($exportProfile, $logger);
     }

    function it_does_not_allow_to_export_data_without_reader_defined(
        $dateProvider,
        $dateConverter,
        $exportJobRepository,
        $entityManager,
        \DateTime $dateTime,
        ExportJobInterface $exportJob,
        ExportProfileInterface $exportProfile,
        LoggerInterface $logger
    ) {
        $dateProvider->getCurrentDate()->willReturn($dateTime);
        $dateConverter->toString($dateTime, 'Y-m-d H:i:s')->willReturn('2015-06-29 12:40:00', '2015-06-29 13:40:00');

        $exportJobRepository->createNew()->willReturn($exportJob);
        $exportJob->getId()->willReturn(2);
        $exportJob->setStartTime($dateTime)->shouldBeCalled();
        $exportJob->getStartTime()->willReturn($dateTime);
        $exportJob->setStatus(Argument::type('string'))->shouldBeCalledTimes(2);
        $exportJob->setProfile($exportProfile)->shouldBeCalled();
        $exportJob->setEndTime($dateTime)->shouldBeCalled();
        $exportJob->getEndTime()->willReturn($dateTime);

        $exportJobRepository->createNew()->willReturn($exportJob);
        $exportProfile->getId()->willReturn(1);
        $exportProfile->addJob($exportJob)->shouldBeCalled();

        $logger->info("Profile: 1; StartTime: 2015-06-29 12:40:00")->shouldBeCalled();
        $logger->error('Profile: 1. Cannot read data with Profile instance without reader defined.')->shouldBeCalled();
        $logger->info("Job: 2; EndTime: 2015-06-29 13:40:00")->shouldBeCalled();

        $entityManager->persist(Argument::type('Sylius\Component\ImportExport\Model\ExportJobInterface'))->shouldBeCalledTimes(2);
        $entityManager->persist(Argument::type('Sylius\Component\ImportExport\Model\ExportProfileInterface'))->shouldBeCalled();
        $entityManager->flush()->shouldBeCalledTimes(2);

        $exportProfile->getId()->willReturn(1);
        $exportProfile->getReader()->willReturn(null);

        $this->shouldThrow(new \InvalidArgumentException('Cannot read data with Profile instance without reader defined.'))
            ->duringExport($exportProfile, $logger);
    }

    function it_does_not_allow_to_export_data_without_writer_defined(
        $dateProvider,
        $exportJobRepository,
        $entityManager,
        $dateConverter,
        \DateTime $dateTime,
        ExportJobInterface $exportJob,
        ExportProfileInterface $exportProfile,
        LoggerInterface $logger
    ) {
        $dateProvider->getCurrentDate()->willReturn($dateTime);
        $dateConverter->toString($dateTime, 'Y-m-d H:i:s')->willReturn('2015-06-29 12:40:00', '2015-06-29 13:40:00');

        $exportJobRepository->createNew()->willReturn($exportJob);
        $exportJob->getId()->willReturn(2);
        $exportJob->setStartTime($dateTime)->shouldBeCalled();
        $exportJob->getStartTime()->willReturn($dateTime);
        $exportJob->setStatus(Argument::type('string'))->shouldBeCalledTimes(2);
        $exportJob->setProfile($exportProfile)->shouldBeCalled();
        $exportJob->setEndTime($dateTime)->shouldBeCalled();
        $exportJob->getEndTime()->willReturn($dateTime);

        $exportJobRepository->createNew()->willReturn($exportJob);
        $exportProfile->getId()->willReturn(1);
        $exportProfile->addJob($exportJob)->shouldBeCalled();

        $logger->info("Profile: 1; StartTime: 2015-06-29 12:40:00")->shouldBeCalled();
        $logger->error('Profile: 1. Cannot write data with Profile instance without writer defined.')->shouldBeCalled();
        $logger->info("Job: 2; EndTime: 2015-06-29 13:40:00")->shouldBeCalled();

        $entityManager->persist(Argument::type('Sylius\Component\ImportExport\Model\ExportJobInterface'))->shouldBeCalledTimes(2);
        $entityManager->persist(Argument::type('Sylius\Component\ImportExport\Model\ExportProfileInterface'))->shouldBeCalled();
        $entityManager->flush()->shouldBeCalledTimes(2);

        $exportProfile->getId()->willReturn(1);
        $exportProfile->getReader()->willReturn('doctrine');
        $exportProfile->getReaderConfiguration()->willReturn(array());
        $exportProfile->getWriter()->willReturn(null);

        $this->shouldThrow(new \InvalidArgumentException('Cannot write data with Profile instance without writer defined.'))
            ->duringExport($exportProfile, $logger);
    }
}
