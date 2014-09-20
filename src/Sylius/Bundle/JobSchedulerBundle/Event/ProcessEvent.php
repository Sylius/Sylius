<?php
/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\JobSchedulerBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Sylius\Bundle\JobSchedulerBundle\Entity\JobInterface;
use Doctrine\ORM\EntityManagerInterface;


/**
 * Process event
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class ProcessEvent extends Event
{
    /**
     * @var \Sylius\Bundle\JobSchedulerBundle\Entity\JobInterface
     */
    private $job;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @param JobInterface           $job
     * @param EntityManagerInterface $em
     */
    public function __construct(JobInterface $job, EntityManagerInterface $em)
    {
        $this->em  = $em;
        $this->job = $job;
    }

    /**
     * Returns entity manager
     *
     * @return EntityManagerInterface
     */
    public function getEm()
    {
        return $this->em;
    }

    /**
     * Returns Job
     *
     * @return JobInterface
     */
    public function getJob()
    {
        return $this->job;
    }

} 