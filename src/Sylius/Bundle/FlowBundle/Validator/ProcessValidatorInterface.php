<?php

namespace Sylius\Bundle\FlowBundle\Validator;

use Sylius\Bundle\FlowBundle\Process\Step\StepInterface;

/**
 * Interface for process validation
 *
 * @author Zach Badgett <zach.badgett@gmail.com>
 */
interface ProcessValidatorInterface
{
    /**
     * Message to display on invalid
     *
     * @param $message
     * @return mixed
     */
    public function setMessage($message);

    /**
     * Return message
     *
     * @return mixed
     */
    public function getMessage();

    /**
     * Set error template
     *
     * @param $template
     * @return mixed
     */
    public function setTemplate($template);

    /**
     * Return error template
     *
     * @return mixed
     */
    public function getTemplate();

    /**
     * Check validation
     *
     * @return boolean
     */
    public function isValid();

    /**
     * @param  StepInterface $step
     * @return mixed
     */
    public function getResponse(StepInterface $step);
}
