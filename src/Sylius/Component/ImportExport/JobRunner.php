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

use Doctrine\Common\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use Sylius\Component\ImportExport\Converter\DateConverter;
use Sylius\Component\ImportExport\Model\Job;
use Sylius\Component\ImportExport\Model\JobInterface;
use Sylius\Component\ImportExport\Model\ProfileInterface;
use Sylius\Component\ImportExport\Provider\CurrentDateProviderInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chruscie@lakion.com>
 */
abstract class JobRunner implements JobRunnerInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    protected $readerRegistry;

    /**
     * @var ServiceRegistryInterface
     */
    protected $writerRegistry;

    /**
     * @var RepositoryInterface
     */
    protected $jobRepository;

    /**
     * @var ObjectManager
     */
    protected $entityManager;

    /**
     * @var CurrentDateProviderInterface
     */
    protected $dateProvider;

    /**
     * @var DateConverter
     */
    protected $dateConverter;

    /**
     * @param CurrentDateProviderInterface $dateProvider
     * @param DateConverter                $dateConverter
     * @param ObjectManager                $entityManager
     * @param RepositoryInterface          $jobRepository
     * @param ServiceRegistryInterface     $readerRegistry
     * @param ServiceRegistryInterface     $writerRegistry
     */
    public function __construct(
        CurrentDateProviderInterface $dateProvider,
        DateConverter $dateConverter,
        ObjectManager $entityManager,
        RepositoryInterface $jobRepository,
        ServiceRegistryInterface $readerRegistry,
        ServiceRegistryInterface $writerRegistry
    ) {
        $this->dateProvider = $dateProvider;
        $this->dateConverter = $dateConverter;
        $this->readerRegistry = $readerRegistry;
        $this->writerRegistry = $writerRegistry;
        $this->jobRepository = $jobRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function start(ProfileInterface $profile, LoggerInterface $logger)
    {
        $job = $this->jobRepository->createNew();

        $job->setStartTime($this->dateProvider->getCurrentDate());
        $job->setStatus(Job::RUNNING);
        $job->setProfile($profile);

        $logger->info(sprintf("Profile: %d; StartTime: %s",
            $profile->getId(),
            $this->dateConverter->toString($job->getStartTime(), 'Y-m-d H:i:s'))
        );

        $profile->addJob($job);

        $this->entityManager->persist($job);
        $this->entityManager->persist($profile);
        $this->entityManager->flush();

        return $job;
    }

    /**
     * {@inheritdoc}
     */
    public function end(JobInterface $job, LoggerInterface $logger, $status)
    {
        $job->setEndTime($this->dateProvider->getCurrentDate());
        $job->setStatus($status);
        $logger->info(sprintf("Job: %d; EndTime: %s",
            $job->getId(),
            $this->dateConverter->toString($job->getEndTime(), 'Y-m-d H:i:s'))
        );

        $this->entityManager->persist($job);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function run(ProfileInterface $profile, LoggerInterface $logger, JobInterface $job)
    {
        $this->validate($job, $logger, $profile);

        $reader = $this->readerRegistry->get($profile->getReader());
        $writer = $this->writerRegistry->get($profile->getWriter());

        while (null !== ($readLine = $reader->read($profile->getReaderConfiguration(), $logger))) {
            $writer->write($readLine, $profile->getWriterConfiguration(), $logger);
        }

        $writer->finalize($job, $profile->getWriterConfiguration());
        $reader->finalize($job);
        $jobStatus = Job::COMPLETED;

        if ($reader->getResultCode() !== 0 || $writer->getResultCode() !== 0) {
            $jobStatus = ($reader->getResultCode() < 0 || $writer->getResultCode() < 0) ? Job::FAILED : Job::ERROR;
        }

        return $jobStatus;
    }

    /**
     * Checks if given exportJob and exportProfile are valid.
     *
     * @param JobInterface     $job
     * @param LoggerInterface  $logger
     * @param ProfileInterface $profile
     */
    protected function validate(JobInterface $job, LoggerInterface $logger, ProfileInterface $profile)
    {
        if (null === $profile->getReader()) {
            $this->generateErrorAction($job, $logger, $profile->getId(), 'read');
        }
        if (null === $profile->getWriter()) {
            $this->generateErrorAction($job, $logger, $profile->getId(), 'write');
        }
    }

    /**
     * Fails job and logs error.
     *
     * @param JobInterface    $job
     * @param LoggerInterface $logger
     * @param integer         $profileId
     * @param string          $type
     *
     * @throws \InvalidArgumentException
     */
    protected function generateErrorAction(JobInterface $job, LoggerInterface $logger, $profileId, $type)
    {
        $this->end($job, $logger, Job::FAILED);
        $logger->error(sprintf('Profile: %d. %s', $profileId, $this->generateErrorMessage($type)));
        throw new \InvalidArgumentException($this->generateErrorMessage($type));
    }

    /**
     * Concat given type with default fail message.
     *
     * @param string $type
     *
     * @return string
     */
    protected function generateErrorMessage($type)
    {
        return sprintf('Cannot %s data with Profile instance without %s defined.', $type, ($type == 'read') ? 'reader' : 'writer');
    }
}
