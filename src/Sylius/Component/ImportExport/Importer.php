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
use Sylius\Component\ImportExport\Model\JobInterface;
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
        Logger $logger) {
        parent::__construct($readerRegistry, $writerRegistry, $importJobRepository, $entityManager, $logger);
    }

    /**
     * {@inheritdoc}
     */
    public function import(ImportProfile $importProfile)
    {
        $job = $this->startJob($importProfile);

        if (null === $readerType = $importProfile->getReader()) {
            $this->logger->addError(sprintf('ImportProfile: %d. Cannot read data with ImportProfile instance without reader defined.', $importProfile->getId()));
            throw new \InvalidArgumentException('Cannot read data with ImportProfile instance without reader defined.');
        }
        if (null === $writerType = $importProfile->getWriter()) {
            $this->logger->addError(sprintf('ImportProfile: %d. Cannot read data with ImportProfile instance without reader defined.', $importProfile->getId()));
            throw new \InvalidArgumentException('Cannot write data with ImportProfile instance without writer defined.');
        }


        $reader = $this->readerRegistry->get($readerType);
        $reader->setConfiguration($exportProfile->getReaderConfiguration());

        $writer = $this->writerRegistry->get($writerType);
        $writer->setConfiguration($exportProfile->getWriterConfiguration());

        foreach ($reader->read() as $data) {    
            $writer->write($data);
        }

        $this->endJob($job);
    }
}