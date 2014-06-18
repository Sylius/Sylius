<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Validator;

use Sylius\Bundle\FlowBundle\Process\Step\StepInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Interface for process validation
 *
 * @author Zach Badgett <zach.badgett@gmail.com>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface ProcessValidatorInterface
{
    /**
     * Message to display on invalid.
     *
     * @param string $message
     *
     * @return ProcessValidatorInterface
     */
    public function setMessage($message);

    /**
     * Return message.
     *
     * @return string
     */
    public function getMessage();

    /**
     * Set step name to go on error.
     *
     * @param string $stepName
     *
     * @return ProcessValidatorInterface
     */
    public function setStepName($stepName);

    /**
     * Return step name to go on error.
     *
     * @return string
     */
    public function getStepName();

    /**
     * Check validation.
     *
     * @return Boolean
     */
    public function isValid();

    /**
     * @param StepInterface $step
     *
     * @return Response
     */
    public function getResponse(StepInterface $step);
}
