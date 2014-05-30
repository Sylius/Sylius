<?php
/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\JobSchedulerBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\JobSchedulerBundle\JobSchedulerEvents;
use Sylius\Bundle\JobSchedulerBundle\Event\ProcessEvent;
use Sylius\Bundle\JobSchedulerBundle\Validator\JobValidatorInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


/**
 * Job manager
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class JobManager implements JobManagerInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Sylius\Bundle\JobSchedulerBundle\Validator\JobValidatorInterface
     */
    private $jobValidator;

    /**
     * @var JoblogFactoryInterface
     */
    private $logFactory;

    /**
     * @var \Symfony\Component\HttpKernel\KernelInterface
     */
    private $kernel;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    private $dispatcher;

    /**
     * @param EntityManagerInterface   $em
     * @param JobValidatorInterface    $jobValidator
     * @param JoblogFactoryInterface   $logFactory
     * @param KernelInterface          $kernel
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        EntityManagerInterface $em,
        JobValidatorInterface $jobValidator,
        JoblogFactoryInterface $logFactory,
        KernelInterface $kernel,
        EventDispatcherInterface $dispatcher)
    {
        $this->em           = $em;
        $this->jobValidator = $jobValidator;
        $this->logFactory   = $logFactory;
        $this->kernel       = $kernel;
        $this->dispatcher   = $dispatcher;

    }

    /**
     * Finds active jobs in repository
     *
     * @return array
     */
    public function findActiveJobs()
    {
        return $this->em->getRepository('SyliusJobSchedulerBundle:Job')->findActiveJobs();
    }


    /**
     *
     * Runs the job and waits until it's finished
     * isRunning sets the report finished time
     *
     * @param $jobId
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function runJobSync($jobId)
    {
        $job = $this->em->getRepository('SyliusJobSchedulerBundle:Job')->findOneById($jobId);

        if (!$job) {
            throw new NotFoundHttpException('Job not found');
        }

        if ($this->jobValidator->isValid($job)) {

            $log = $this->logFactory->createLog($job);
            $log->registerStart();

            if ($job->getIsRunning()) {
                $log->addError('Process already running');

            } else {
                $event = new ProcessEvent($job, $this->em);
                $this->dispatcher->dispatch(JobSchedulerEvents::PROCESS_STARTED, $event);
                $job->setIsRunning(true);
                $this->saveJob($job);

                //Synchronous call
                $process = new Process($job->getCommand());
                $process->run(function ($type, $buffer) use ($log) {
                    if (Process::ERR === $type) {
                        $log->addError($buffer);
                    } else {
                        $log->addOutput($buffer);
                    }
                });

                $job->setIsRunning(false);
                $log->registerEnd();

                $this->dispatcher->dispatch(JobSchedulerEvents::PROCESS_ENDED, $event);
            }

            $this->saveJob($job);
        }

    }

    /**
     * The job is dispatched asynchronously to a process that runs it synchronously
     *
     * @param $jobId
     */
    public function runJobAsync($jobId)
    {
        $command = $this->kernel->getRootDir() . "/console sylius:run_single_job_synchronous " . $jobId . " --env=" . $this->kernel->getEnvironment();
        $process = new Process($command);
        $process->start();
    }

    /**
     * Saves a job
     *
     * @param $job
     */
    private function saveJob($job)
    {
        $this->em->persist($job);
        $this->em->flush();
    }
} 