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

use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
use Sylius\Bundle\FlowBundle\Process\Step\StepInterface;

/**
 * Default process validator.
 *
 * @author Zach Badgett <zach.badgett@gmail.com>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ProcessValidator implements ProcessValidatorInterface
{
    /**
     * @var string|null
     */
    protected $message;

    /**
     * @var string|null
     */
    protected $stepName;

    /**
     * @var callable
     */
    protected $validation;

    public function __construct($message = null, $stepName = null, \Closure $validation = null)
    {
        $this->message = $message;
        $this->stepName = $stepName;
        $this->validation = $validation;
    }

    /**
     * {@inheritdoc}
     */
    public function setStepName($stepName)
    {
        $this->stepName = $stepName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStepName()
    {
        return $this->stepName;
    }

    /**
     * {@inheritdoc}
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set validation.
     *
     * @param callable $validation
     *
     * @return $this
     */
    public function setValidation(\Closure $validation)
    {
        $this->validation = $validation;

        return $this;
    }

    /**
     * Get validation.
     *
     * @return callable
     */
    public function getValidation()
    {
        return $this->validation;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid(ProcessContextInterface $processContext)
    {
        return call_user_func($this->validation) ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse(StepInterface $step)
    {
        if ($this->getStepName()) {
            return $step->proceed($this->getStepName());
        }

        throw new ProcessValidatorException(400, $this->getMessage());
    }
}
