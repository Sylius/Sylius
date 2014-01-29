<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Event;

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Resource event.
 *
 * @author Jérémy Leherpeur <jeremy@lehepeur.net>
 */
class ResourceEvent extends GenericEvent
{
    const TYPE_ERROR    = 'error';
    const TYPE_WARNING  = 'warning';
    const TYPE_INFO     = 'info';
    const TYPE_SUCCESS  = 'success';

    public function stop($message, $type = self::TYPE_ERROR, $params = array())
    {
        $this->error = true;
        $this->messageType = $type;
        $this->message = $message;
        $this->messageParams = $params;
        $this->stopPropagation();
    }

    /**
     * Indicate if an error has been detected
     *
     * @var Boolean
     */
    protected $error = false;

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
    protected $messageParams = array();

    /**
     * Get error property
     *
     * @return Boolean $error
     */
    public function isStopped()
    {
        return $this->error === true;
    }

    /**
     * Get messageType property
     *
     * @return string $messageType
     */
    public function getMessageType()
    {
        return $this->messageType;
    }

    /**
     * Get message property
     *
     * @return string $message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Get messageParams property
     *
     * @return string $messageParams
     */
    public function getMessageParams()
    {
        return $this->messageParams;
    }
}
