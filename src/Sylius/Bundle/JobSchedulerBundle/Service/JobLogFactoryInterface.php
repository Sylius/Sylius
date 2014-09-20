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

use Sylius\Bundle\JobSchedulerBundle\Entity\JobInterface;


/**
 * Job log factory interface
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface JobLogFactoryInterface
{


    /**
     * Creates a Log
     *
     * @param JobInterface $job
     *
     * @return mixed
     */
    public function createLog(JobInterface $job);
} 