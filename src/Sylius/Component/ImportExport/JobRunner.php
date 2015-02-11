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
use Monolog\Handler\StreamHandler;
use Sylius\Component\ImportExport\Model\Job;
use Sylius\Component\ImportExport\Model\JobInterface;
use Sylius\Component\ImportExport\Model\ProfileInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
* @author Łukasz Chruściel <lukasz.chruscie@lakion.com>
*/
class JobRunner
{
    /**
     * Reader registry
     *
     * @var ServiceRegistryInterface
     */
    protected $readerRegistry;

    /**
     * Writer registry
     *
     * @var ServiceRegistryInterface
     */
    protected $writerRegistry;

    /**
     * import job repository
     *
     * @var RepositoryInterface
     */
    protected $jobRepository;

    /**
     * Entity manager
     *
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * Logger for importer
     *
     * @var Logger
     */    
    protected $logger;

    /**
     * Constructor
     *
     * @var ServiceRegistryInterface $readerRegistry
     * @var ServiceRegistryInterface $writerRegistry
     * @var RepositoryInterface $jobRepository
     * @var EntityManager $entityManager
     * @var Logger $logger
     */
    public function __construct(
        ServiceRegistryInterface $readerRegistry, 
        ServiceRegistryInterface $writerRegistry,
        RepositoryInterface $jobRepository,
        EntityManager $entityManager,
        Logger $logger)
    {
        $this->readerRegistry = $readerRegistry;
        $this->writerRegistry = $writerRegistry;
        $this->jobRepository = $jobRepository;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * Create import job
     *
     * @param ProfileInterface $profile
     * @return JobInterface
     */
    protected function startJob(ProfileInterface $profile)
    {
        $job = $this->jobRepository->createNew();

        $job->setStartTime(new \DateTime());
        $job->setStatus(Job::RUNNING);
        $job->setProfile($profile);

        $this->logger->pushHandler(new StreamHandler(sprintf('app/logs/export_job_%d_%s.log', $profile->getId(), $job->getStartTime()->format('Y_m_d_H_i_s'))));
        $this->logger->addInfo(sprintf("Profile: %d; StartTime: %s", $profile->getId(), $job->getStartTime()->format('Y-m-d H:i:s')));

        $profile->addJob($job);

        $this->entityManager->persist($job);
        $this->entityManager->persist($profile);
        $this->entityManager->flush();

        return $job;
    }

    /**
     * End import job 
     *
     * @param JobInterface $job
     */
    protected function endJob(JobInterface $job) 
    {
        $job->setUpdatedAt(new \DateTime());
        $job->setEndTime(new \DateTime());
        $job->setStatus(Job::COMPLETED);
        $this->logger->addInfo(sprintf("Job: %d; EndTime: %s", $job->getId(), $job->getEndTime()->format('Y-m-d H:i:s')));

        $this->entityManager->persist($job);
        $this->entityManager->flush();
    }
}