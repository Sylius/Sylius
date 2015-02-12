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

use Sylius\Component\ImportExport\Model\ImportProfile;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Doctrine\ORM\EntityManager;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\ImportExport\Model\Job;
use Monolog\Logger;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class Importer extends JobRunner implements ImporterInterface
{
    /**
     * {@inheritdoc}
     */
    public function __construct(
        ServiceRegistryInterface $readerRegistry,
        ServiceRegistryInterface $writerRegistry,
        RepositoryInterface $importJobRepository,
        EntityManager $entityManager,
        Logger $logger)
    {
        parent::__construct($readerRegistry, $writerRegistry, $importJobRepository, $entityManager, $logger);
    }

    /**
     * {@inheritdoc}
     */
    public function import(ImportProfile $importProfile)
    {
        $job = $this->startJob($importProfile);

        if (null === $readerType = $importProfile->getReader()) {
            $this->endJob($job, Job::FAILED);
            $this->logger->addError(sprintf('ImportProfile: %d. Cannot read data with ImportProfile instance without reader defined.', $importProfile->getId()));
            throw new \InvalidArgumentException('Cannot read data with ImportProfile instance without reader defined.');
        }
        if (null === $writerType = $importProfile->getWriter()) {
            $this->endJob($job, Job::FAILED);
            $this->logger->addError(sprintf('ImportProfile: %d. Cannot read data with ImportProfile instance without reader defined.', $importProfile->getId()));
            throw new \InvalidArgumentException('Cannot write data with ImportProfile instance without writer defined.');
        }


        $reader = $this->readerRegistry->get($readerType);

        $reader->setConfiguration($importProfile->getReaderConfiguration(), $this->logger);

        $writer = $this->writerRegistry->get($writerType);
        $writer->setConfiguration($importProfile->getWriterConfiguration(), $this->logger);

        while (null !== ($readLine = $reader->read())) {
            $writer->write($readLine);
        }

        $writer->finalize($job);
        $reader->finalize($job);

        $jobStatus = Job::COMPLETED;

        if ($reader->getResultCode() !== 0 || $writer->getResultCode() !== 0) {
            $jobStatus = ($reader->getResultCode() < 0 || $writer->getResultCode() < 0) ? Job::FAILED : Job::ERROR;
        }

        $this->endJob($job, $jobStatus);
    }
}
