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

use Symfony\Component\HttpFoundation\Response;

use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;

interface StepInterface
{
    /**
     * Get step name in current scenario.
     *
     * @return string
     */
    public function getName();

    /**
     * Set step name.
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Display action.
     *
     * @param ProcessContextInterface $context
     *
     * @return Response
     */
    public function displayAction(ProcessContextInterface $context);

    /**
     * Forward action.
     *
     * @param ProcessContextInterface $context
     *
     * @return null|Response
     */
    public function forwardAction(ProcessContextInterface $context);

    /**
     * Is step active in process?
     *
     * @return Boolean
     */
    public function isActive();

    /**
     * Proceeds to the next step.
     *
     * @return ActionResult
     */
    public function complete();

    /**
     * Proceeds to the given step.
     *
     * @param string $nextStepName
     *
     * @return ActionResult
     */
    public function proceed($nextStepName);
}
