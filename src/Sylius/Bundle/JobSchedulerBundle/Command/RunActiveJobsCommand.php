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

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * sylius:run_active_jobs Command
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class RunActiveJobsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sylius:run_active_jobs')
            ->setDescription('Run Active Cron Jobs');
    }

    /**
     * Command that runs active jobs
     *
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('sylius.scheduler.manager')->runActiveJobs();
    }
} 