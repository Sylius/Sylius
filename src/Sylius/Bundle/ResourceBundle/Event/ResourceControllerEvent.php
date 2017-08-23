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
    public function stop(string $message, string $type = self::TYPE_ERROR, array $parameters = [], int $errorCode = 500)
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
    public function isStopped(): bool
    {
        return $this->isPropagationStopped();
    }

    /**
     * @return string
     */
    public function getMessageType(): string
    {
        return $this->messageType;
    }

    /**
     * @param string $messageType Should be one of ResourceEvent's TYPE constants
     */
    public function setMessageType($messageType): void
    {
        $this->messageType = $messageType;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return array
     */
    public function getMessageParameters(): array
    {
        return $this->messageParameters;
    }

    /**
     * @param array $messageParameters
     */
    public function setMessageParameters(array $messageParameters): void
    {
        $this->messageParameters = $messageParameters;
    }

    /**
     * @return int
     */
    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    /**
     * @param int $errorCode
     */
    public function setErrorCode(int $errorCode): void
    {
        $this->errorCode = $errorCode;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }

    /**
     * @return bool
     */
    public function hasResponse(): bool
    {
        return null !== $this->response;
    }

    /**
     * @return Response|null
     */
    public function getResponse(): ?Response
    {
        return $this->response;
    }
}
