<?php
/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sylius\Bundle\JobSchedulerBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;

use Symfony\Component\Console\Output\OutputInterface;


/**
 * sylius:run_single_job_synchronous Command
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class RunSingleJobSynchronousCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sylius:run_single_job_synchronous')
            ->setDescription('Run Single Job')
            ->addArgument(
                'jobId',
                InputArgument::REQUIRED,
                'What job do you want to run?'
            );
    }

    /**
     * Runs a job synchronously
     *
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jobId = $input->getArgument('jobId');
        $this->getContainer()->get('sylius.scheduler.job.manager')->runJobSync($jobId);
    }
} 