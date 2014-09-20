<?php
/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\JobSchedulerBundle\Entity\Repository;

use Sylius\Component\Resource\Repository\RepositoryInterface;


/**
 * Job repository interface
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface JobRepositoryInterface extends RepositoryInterface
{
    /**
     * Find active  jobs.
     *
     * @return array
     */
    public function findActiveJobs();
} 