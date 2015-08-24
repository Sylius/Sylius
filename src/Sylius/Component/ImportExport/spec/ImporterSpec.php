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
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Sylius\Component\ImportExport\Converter\DateConverter;
use Sylius\Component\ImportExport\Logger\Factory\StreamHandlerFactoryInterface;
use Sylius\Component\ImportExport\Logger\ImportExportLogger;
use Sylius\Component\ImportExport\Model\ImportJobInterface;
use Sylius\Component\ImportExport\Model\ImportProfileInterface;
use Sylius\Component\ImportExport\Provider\CurrentDateProviderInterface;
use Sylius\Component\ImportExport\Reader\ReaderInterface;
use Sylius\Component\ImportExport\Writer\WriterInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ImporterSpec extends ObjectBehavior
{
    function let(
        CurrentDateProviderInterface $dateProvider,
        DateConverter $dateConverter,
        EntityManager $entityManager,
        RepositoryInterface $importJobRepository,
        ServiceRegistryInterface $readerRegistry,
        ServiceRegistryInterface $writerRegistry
    ) {
        $this->beConstructedWith(
            $dateProvider,
            $dateConverter,
            $entityManager,
            $importJobRepository,
            $readerRegistry,
            $writerRegistry
        );
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
        $dateProvider,
        $dateConverter,
        $importJobRepository,
        $entityManager,
        $readerRegistry,
        $writerRegistry,
        \DateTime $dateTime,
        ImportJobInterface $importJob,
        ImportProfileInterface $importProfile,
        LoggerInterface $logger,
        ReaderInterface $reader,
        WriterInterface $writer
    ) {
        $dateProvider->getCurrentDate()->willReturn($dateTime);
        $dateConverter->toString($dateTime, 'Y-m-d H:i:s')->willReturn('2015-06-29 12:40:00', '2015-06-29 13:40:00');

        $importJobRepository->createNew()->willReturn($importJob);
        $importJob->getId()->willReturn(2);
        $importJob->setStartTime($dateTime)->shouldBeCalled();
        $importJob->getStartTime()->willReturn($dateTime);
        $importJob->setStatus(Argument::type('string'))->shouldBeCalledTimes(2);
        $importJob->setProfile($importProfile)->shouldBeCalled();
        $importJob->setEndTime($dateTime)->shouldBeCalled();
        $importJob->getEndTime()->willReturn($dateTime);

        $logger->info("Job: 2; EndTime: 2015-06-29 13:40:00")->shouldBeCalled();
        $logger->info("Profile: 1; StartTime: 2015-06-29 12:40:00")->shouldBeCalled();

        $entityManager->persist(Argument::type('Sylius\Component\ImportExport\Model\ImportJobInterface'))->shouldBeCalledTimes(2);
        $entityManager->persist(Argument::type('Sylius\Component\ImportExport\Model\ImportProfileInterface'))->shouldBeCalled();
        $entityManager->flush()->shouldBeCalledTimes(2);

        $importProfile->getId()->willReturn(1);
        $importProfile->getReader()->willReturn('doctrine');
        $importProfile->getReaderConfiguration()->willReturn(array());
        $importProfile->getWriter()->willReturn('csv');
        $importProfile->getWriterConfiguration()->willReturn(array());
        $importProfile->addJob($importJob)->shouldBeCalled();

        $readerRegistry->get('doctrine')->willReturn($reader);
        $reader->read(array(), $logger)->willReturn(array('readData1'), array('readData2'), null);
        $reader->finalize($importJob)->shouldBeCalled();
        $reader->getResultCode()->willReturn(0);

        $writerRegistry->get('csv')->willReturn($writer);
        $writer->write(array('readData1'), array(), $logger)->shouldBeCalled();
        $writer->write(array('readData2'), array(), $logger)->shouldBeCalled();
        $writer->finalize($importJob, array())->shouldBeCalled();
        $writer->getResultCode()->willReturn(0);

        $this->import($importProfile, $logger);
    }

     function it_does_not_allow_to_import_data_without_reader_defined(
         $dateProvider,
         $dateConverter,
         $importJobRepository,
         $entityManager,
         \DateTime $dateTime,
         ImportJobInterface $importJob,
         ImportProfileInterface $importProfile,
         LoggerInterface $logger
     ) {
         $dateProvider->getCurrentDate()->willReturn($dateTime);
         $dateConverter->toString($dateTime, 'Y-m-d H:i:s')->willReturn('2015-06-29 12:40:00', '2015-06-29 13:40:00');

         $importJobRepository->createNew()->willReturn($importJob);
         $importJob->getId()->willReturn(2);
         $importJob->setStartTime($dateTime)->shouldBeCalled();
         $importJob->getStartTime()->willReturn($dateTime);
         $importJob->setStatus(Argument::type('string'))->shouldBeCalledTimes(2);
         $importJob->setProfile($importProfile)->shouldBeCalled();
         $importJob->setEndTime($dateTime)->shouldBeCalled();
         $importJob->getEndTime()->willReturn($dateTime);

         $importJobRepository->createNew()->willReturn($importJob);
         $importProfile->getId()->willReturn(1);
         $importProfile->addJob($importJob)->shouldBeCalled();

         $logger->info("Profile: 1; StartTime: 2015-06-29 12:40:00")->shouldBeCalled();
         $logger->error('Profile: 1. Cannot read data with Profile instance without reader defined.')->shouldBeCalled();
         $logger->info("Job: 2; EndTime: 2015-06-29 13:40:00")->shouldBeCalled();

         $entityManager->persist(Argument::type('Sylius\Component\ImportExport\Model\ImportJobInterface'))->shouldBeCalledTimes(2);
         $entityManager->persist(Argument::type('Sylius\Component\ImportExport\Model\ImportProfileInterface'))->shouldBeCalled();
         $entityManager->flush()->shouldBeCalledTimes(2);

         $importProfile->getId()->willReturn(1);
         $importProfile->getReader()->willReturn(null);

         $this->shouldThrow(new \InvalidArgumentException('Cannot read data with Profile instance without reader defined.'))
         ->duringImport($importProfile, $logger);
     }

    function it_does_not_allow_to_import_data_without_writer_defined(
        $dateProvider,
        $dateConverter,
        $importJobRepository,
        $entityManager,
        \DateTime $dateTime,
        ImportJobInterface $importJob,
        ImportProfileInterface $importProfile,
        LoggerInterface $logger
    ) {
        $dateProvider->getCurrentDate()->willReturn($dateTime);
        $dateConverter->toString($dateTime, 'Y-m-d H:i:s')->willReturn('2015-06-29 12:40:00', '2015-06-29 13:40:00');

        $importJobRepository->createNew()->willReturn($importJob);
        $importJob->getId()->willReturn(2);
        $importJob->setStartTime($dateTime)->shouldBeCalled();
        $importJob->getStartTime()->willReturn($dateTime);
        $importJob->setStatus(Argument::type('string'))->shouldBeCalledTimes(2);
        $importJob->setProfile($importProfile)->shouldBeCalled();
        $importJob->setEndTime($dateTime)->shouldBeCalled();
        $importJob->getEndTime()->willReturn($dateTime);


        $importJobRepository->createNew()->willReturn($importJob);
        $importProfile->getId()->willReturn(1);
        $importProfile->addJob($importJob)->shouldBeCalled();

        $logger->info("Profile: 1; StartTime: 2015-06-29 12:40:00")->shouldBeCalled();
        $logger->error('Profile: 1. Cannot write data with Profile instance without writer defined.')->shouldBeCalled();
        $logger->info("Job: 2; EndTime: 2015-06-29 13:40:00")->shouldBeCalled();

        $entityManager->persist(Argument::type('Sylius\Component\ImportExport\Model\ImportJobInterface'))->shouldBeCalledTimes(2);
        $entityManager->persist(Argument::type('Sylius\Component\ImportExport\Model\ImportProfileInterface'))->shouldBeCalled();
        $entityManager->flush()->shouldBeCalledTimes(2);

        $importProfile->getId()->willReturn(1);
        $importProfile->getReader()->willReturn('doctrine');
        $importProfile->getReaderConfiguration()->willReturn(array());
        $importProfile->getWriter()->willReturn(null);

        $this->shouldThrow(new \InvalidArgumentException('Cannot write data with Profile instance without writer defined.'))
            ->duringImport($importProfile, $logger);
    }
}
