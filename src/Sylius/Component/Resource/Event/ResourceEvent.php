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
 * @author Jérémy Leherpeur <jeremy@leherpeur.net>
 */
class ResourceEvent extends GenericEvent
{
    const TYPE_ERROR = 'error';
    const TYPE_WARNING = 'warning';
    const TYPE_INFO = 'info';
    const TYPE_SUCCESS = 'success';

    /**
     * @var string
     */
    protected $messageType = '';

    /**
     * @var string
     */
    protected $message = '';

    /**
     * @var array
     */
    protected $messageParameters = [];

    /**
     * @var int
     */
    protected $errorCode = 500;

    /**
     * @param string $message
     * @param string $type
     * @param array $parameters
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
     * @return bool
     */
    public function isStopped()
    {
        return $this->isPropagationStopped();
    }

    /**
     * @return string
     */
    public function getMessageType()
    {
        return $this->messageType;
    }

    /**
     * @param string $messageType Should be one of ResourceEvent's TYPE constants
     */
    public function setMessageType($messageType)
    {
        $this->messageType = $messageType;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return array
     */
    public function getMessageParameters()
    {
        return $this->messageParameters;
    }

    /**
     * @param array $messageParameters
     */
    public function setMessageParameters(array $messageParameters)
    {
        $this->messageParameters = $messageParameters;
    }

    /**
     * @return int
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @param int $errorCode
     */
    public function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;
    }
}
