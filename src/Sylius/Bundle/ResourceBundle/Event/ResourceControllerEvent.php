<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ResourceBundle\Event;

use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Jérémy Leherpeur <jeremy@leherpeur.net>
 */
class ResourceControllerEvent extends GenericEvent
{
    public const TYPE_ERROR = 'error';
    public const TYPE_WARNING = 'warning';
    public const TYPE_INFO = 'info';
    public const TYPE_SUCCESS = 'success';

    /**
     * @var string
     */
    private $messageType = '';

    /**
     * @var string
     */
    private $message = '';

    /**
     * @var array
     */
    private $messageParameters = [];

    /**
     * @var int
     */
    private $errorCode = 500;

    /**
     * @var Response
     */
    private $response;

    /**
     * @param string $message
     * @param string $type
     * @param array $parameters
     * @param int $errorCode
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

    /**
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return bool
     */
    public function hasResponse()
    {
        return null !== $this->response;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
