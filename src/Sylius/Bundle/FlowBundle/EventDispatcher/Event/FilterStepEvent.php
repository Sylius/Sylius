<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\EventDispatcher\Event;

use Sylius\Bundle\FlowBundle\Process\Step\StepInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Step filter event.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class FilterStepEvent extends Event
{
    /**
     * Step.
     *
     * @var StepInterface
     */
    protected $step;

    /**
     * Constructor.
     *
     * @param StepInterface $step
     */
    public function __construct(StepInterface $step)
    {
        $this->step = $step;
    }

    /**
     * Get step.
     *
     * @return StepInterface
     */
    public function getStep()
    {
        return $this->step;
    }
}
