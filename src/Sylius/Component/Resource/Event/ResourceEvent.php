<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\Event;

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Resource event.
 *
 * @author Jérémy Leherpeur <jeremy@leherpeur.net>
 */
class ResourceEvent extends GenericEvent
{
    const TYPE_ERROR = 'error';
    const TYPE_WARNING = 'warning';
    const TYPE_INFO = 'info';
    const TYPE_SUCCESS = 'success';

    /**
     * Message type
     *
     * @var string
     */
    protected $messageType = '';

    /**
     * Message
     *
     * @var string
     */
    protected $message = '';

    /**
     * Message parameters
     *
     * @var array
     */
    protected $messageParameters = [];

    /**
     * ErrorCode
     *
     * @var int
     */
    protected $errorCode = 500;

    /**
     * Stop event propagation
     *
     * @param string $message
     * @param string $type
     * @param array  $parameters
     */
    public function stop($message, $type = self::TYPE_ERROR, $parameters = [], $errorCode = 500)
    {
        $this->messageType = $type;
        $this->message = $message;
        $this->messageParameters = $parameters;
        $this->errorCode = $errorCode;

        $this->stopPropagation();
    }

    /**
     * Alias
     *
     * @return bool
     */
    public function isStopped()
    {
        return $this->isPropagationStopped();
    }

    /**
     * Get messageType property
     *
     * @return string
     */
    public function getMessageType()
    {
        return $this->messageType;
    }

    /**
     * Sets messageType property
     *
     * @param string $messageType Should be one of ResourceEvent's TYPE constants
     *
     * @return $this
     */
    public function setMessageType($messageType)
    {
        $this->messageType = $messageType;

        return $this;
    }

    /**
     * Get message property
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Sets message property
     *
     * @param string $message
     *
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get messageParameters property
     *
     * @return array
     */
    public function getMessageParameters()
    {
        return $this->messageParameters;
    }

    /**
     * Sets messageParameters property
     *
     * @param array $messageParameters
     *
     * @return $this
     */
    public function setMessageParameters(array $messageParameters)
    {
        $this->messageParameters = $messageParameters;

        return $this;
    }

    /**
     * Get errorCode property
     *
     * @return int
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * Sets errorCode property
     *
     * @param int $errorCode
     *
     * @return $this
     */
    public function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;

        return $this;
    }
}
