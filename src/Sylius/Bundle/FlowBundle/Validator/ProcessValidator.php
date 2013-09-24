<?php

namespace Sylius\Bundle\FlowBundle\Validator;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Sylius\Bundle\FlowBundle\Process\Step\StepInterface;

/**
 * Default process validator
 *
 * @author Zach Badgett <zach.badgett@gmail.com>
 */
class ProcessValidator implements ProcessValidatorInterface
{
    /** @var null|string  */
    protected $template;
    /** @var null|string  */
    protected $message;
    /** @var callable  */
    protected $validation;

    public function __construct(\Closure $validation, $message = null, $template = null)
    {
        $this->validation = $validation;
        $this->message = $message;
        $this->template = $template;
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate()
    {
        return $this->template;
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
     * Set validation
     *
     * @param callable $validation
     * @return $this
     */
    public function setValidation(\Closure $validation)
    {
        $this->validation = $validation;

        return $this;
    }

    /**
     * Get validation
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
    public function isValid()
    {
        return (call_user_func($this->validation)) ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse(StepInterface $step)
    {
        if ($this->getTemplate()) {
            return $step->render($this->getTemplate(), array('error' => $this->getMessage()));
        }

        throw new HttpException(400, $this->getMessage());
    }
}
