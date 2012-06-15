<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Process\Step;

use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;

interface StepInterface
{
    /**
     * Get step name in current scenario.
     *
     * @return string
     */
    function getName();

    /**
     * Set step name.
     *
     * @param strgin $step
     */
    function setName($name);

    /**
     * Display action.
     *
     * @param ProcessContextInterface $context
     *
     * @return Response
     */
    function displayAction(ProcessContextInterface $context);

    /**
     * Forward action.
     *
     * @param ProcessContextInterface $context
     *
     * @return null|Response
     */
    function forwardAction(ProcessContextInterface $context);

    /**
     * Is step active in process?
     *
     * @return Boolean
     */
    function isActive();
}
