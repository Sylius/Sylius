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

use Sylius\Bundle\FlowBundle\Process\ProcessInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Process filter event.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class FilterProcessEvent extends Event
{
    /**
     * Process.
     *
     * @var ProcessInterface
     */
    protected $process;

    /**
     * Constructor.
     *
     * @param ProcessInterface $process
     */
    public function __construct(ProcessInterface $process)
    {
        $this->process = $process;
    }

    /**
     * Get process.
     *
     * @return ProcessInterface
     */
    public function getProcess()
    {
        return $this->process;
    }
}
