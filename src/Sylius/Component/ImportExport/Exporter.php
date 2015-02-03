<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\ImportExport;

use Sylius\Component\ImportExport\Model\ExportProfileInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Doctrine\ORM\EntityManager;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\ImportExport\Model\Job;
use Sylius\Component\ImportExport\Model\JobInterface;
use Monolog\Logger;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class Exporter implements ExporterInterface
{
    /**
     * Reader registry
     *
     * @var ServiceRegistryInterface
     */
    private $readerRegistry;

    /**
     * Writer registry
     *
     * @var ServiceRegistryInterface
     */
    private $writerRegistry;

    /**
     * Export job repository
     *
     * @var RepositoryInterface
     */
    private $exportJobRepository;

    /**
     * Entity manager
     *
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Logger for exporter
     *
     * @var Logger
     */    
    private $logger;

    /**
     * Constructor
     *
     * @var ServiceRegistryInterface $readerRegistry
     * @var ServiceRegistryInterface $writerRegistry
     */
    public function __construct(ServiceRegistryInterface $readerRegistry, ServiceRegistryInterface $writerRegistry, RepositoryInterface $exportJobRepository, EntityManager $entityManager, Logger $logger)
    {
        $this->readerRegistry = $readerRegistry;
        $this->writerRegistry = $writerRegistry;
        $this->exportJobRepository = $exportJobRepository;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    public function export(ExportProfileInterface $exportProfile)
    {
        $exportJob = $this->startExportJob($exportProfile);

        if (null === $readerType = $exportProfile->getReader()) {
            $this->logger->error(sprintf('ExportProfile: %d. Cannot read data with ExportProfile instance without reader defined.', $exportProfile->getId()));
            throw new \InvalidArgumentException('Cannot read data with ExportProfile instance without reader defined.');
        }
        if (null === $writerType = $exportProfile->getWriter()) {
            $this->logger->error(sprintf('ExportProfile: %d. Cannot read data with ExportProfile instance without reader defined.', $exportProfile->getId()));
            throw new \InvalidArgumentException('Cannot write data with ExportProfile instance without writer defined.');
        }

        $reader = $this->readerRegistry->get($readerType);
        $reader->setConfiguration($exportProfile->getReaderConfiguration());

        $writer = $this->writerRegistry->get($writerType);
        $writer->setConfiguration($exportProfile->getWriterConfiguration());

        foreach ($reader->read() as $data) {
            $writer->write($data);
        }

        $this->endExportJob($exportJob);
    }

    /**
     * Create export job
     *
     * @param ExportProfileInterface $exportProfile
     * @return JobInterface
     */
    private function startExportJob(ExportProfileInterface $exportProfile)
    {
        $exportJob = $this->exportJobRepository->createNew();
        $exportJob->setStartTime(new \DateTime());
        $exportJob->setStatus(Job::RUNNING);
        $exportJob->setExportProfile($exportProfile);
        $this->logger->info(sprintf("ExportProfile: %d; StartTime: %s", $exportProfile->getId(), $exportJob->getStartTime()->format('Y-m-d H:i:s')));

        $exportProfile->addJob($exportJob);

        $this->entityManager->persist($exportJob);
        $this->entityManager->persist($exportProfile);
        $this->entityManager->flush();

        return $exportJob;
    }

    /**
     * End export job 
     *
     * @param JobInterface $exportJob
     */
    private function endExportJob(JobInterface $exportJob) 
    {
        $exportJob->setUpdatedAt(new \DateTime());
        $exportJob->setEndTime(new \DateTime());
        $exportJob->setStatus(Job::COMPLETED);
        $this->logger->info(sprintf("Exportjob: %d; EndTime: %s", $exportJob->getId(), $exportJob->getEndTime()->format('Y-m-d H:i:s')));

        $this->entityManager->persist($exportJob);
        $this->entityManager->flush();
    }
}