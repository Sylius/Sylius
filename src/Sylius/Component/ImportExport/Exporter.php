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

use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use Sylius\Component\ImportExport\Model\ExportProfileInterface;
use Sylius\Component\ImportExport\Model\Job;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class Exporter extends JobRunner implements ExporterInterface
{
    /**
     * {@inheritdoc}
     */
    public function __construct(
        ServiceRegistryInterface $readerRegistry,
        ServiceRegistryInterface $writerRegistry,
        RepositoryInterface $exportJobRepository,
        EntityManager $entityManager,
        Logger $logger)
    {
        parent::__construct($readerRegistry, $writerRegistry, $exportJobRepository, $entityManager, $logger);
    }

    /**
     * {@inheritdoc}
     */
    public function export(ExportProfileInterface $exportProfile)
    {
        $exportJob = $this->startJob($exportProfile);

        $this->validate($exportJob, $exportProfile);

        $reader = $this->readerRegistry->get($exportProfile->getReader());
        $reader->setConfiguration($exportProfile->getReaderConfiguration(), $this->logger);

        $writerConfiguration = $exportProfile->getWriterConfiguration();
        $writer = $this->writerRegistry->get($exportProfile->getWriter());

        $writer->setConfiguration($exportProfile->getWriterConfiguration(), $this->logger);

        while (null !== ($readLine = $reader->read())) {
            $writer->write($readLine);
        }

        $writer->finalize($exportJob);
        $reader->finalize($exportJob);

        $jobStatus = Job::COMPLETED;

        if ($reader->getResultCode() !== 0 || $writer->getResultCode() !== 0) {
            $jobStatus = ($reader->getResultCode() < 0 || $writer->getResultCode() < 0) ? Job::FAILED : Job::ERROR;
        }

        $this->endJob($exportJob, $jobStatus);
    }

    private function validate($exportJob, $exportProfile)
    {
        if (null === $exportProfile->getReader()) {
            $this->generateErrorAction($exportJob, $exportProfile->getId(), 'read');
        }
        if (null === $exportProfile->getWriter()) {
            $this->generateErrorAction($exportJob, $exportProfile->getId(), 'write');
        }
    }

    private function generateErrorAction($exportJob, $exportProfileId, $type)
    {
        $this->endJob($exportJob, Job::FAILED);
        $this->logger->addError(sprintf('ExportProfile: %d. %s', $exportProfileId, $this->generateErrorMessage($type)));
        throw new \InvalidArgumentException($this->generateErrorMessage($type));
    }

    private function generateErrorMessage($type)
    {
        return sprintf('Cannot %s data with ExportProfile instance without %s defined.', $type, ($type == 'read') ? 'reader' : 'writer');
    }

    private function validate($exportJob, $exportProfile)
    {
        if (null === $exportProfile->getReader()) {
            $this->generateErrorAction($exportJob, $exportProfile->getId(), 'read');
        }
        if (null === $exportProfile->getWriter()) {
            $this->generateErrorAction($exportJob, $exportProfile->getId(), 'write');
        }
    }

    private function generateErrorAction($exportJob, $exportProfileId, $type)
    {
        $this->endJob($exportJob, Job::FAILED);
        $this->logger->addError(sprintf('ExportProfile: %d. %s', $exportProfileId, $this->generateErrorMessage($type)));
        throw new \InvalidArgumentException($this->generateErrorMessage($type));
    }

    private function generateErrorMessage($type)
    {
        return sprintf('Cannot %s data with ExportProfile instance without %s defined.', $type, ($type == 'read') ? 'reader' : 'writer');
    }
}
