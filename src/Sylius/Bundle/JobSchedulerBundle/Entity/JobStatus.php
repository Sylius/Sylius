<?php
/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\JobSchedulerBundle\Entity;

/**
 * Job status entity
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class JobStatus
{
    const SUCCESS = 0;

    const FAILED = 1;

    const RUNNING = 2;
} 