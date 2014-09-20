<?php
/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\JobSchedulerBundle;


/**
 * Job scheduler events
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
final class JobSchedulerEvents
{
    const PROCESS_STARTED = 'sylius.process.started';

    const PROCESS_ENDED = 'sylius.process.ended';
} 