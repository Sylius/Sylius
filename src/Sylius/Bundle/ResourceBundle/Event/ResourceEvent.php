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

    public function stopWithError($message, $params = array())
    {
        $this->error = true;
        $this->message_type = self::TYPE_ERROR;
        $this->message = $message;
        $this->message_params = $params;
        $this->stopPropagation();
    }

    public function stopWithWarning($message, $params = array())
    {
        $this->error = true;
        $this->message_type = self::TYPE_WARNING;
        $this->message = $message;
        $this->message_params = $params;
        $this->stopPropagation();
    }

    /**
     * Indicate if an error has been detected
     *
     * @var boolean
     */
    protected $error = false;

    /**
     * Message type
     *
     * @var string
     */
    protected $message_type = '';

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
    protected $message_params = array();

    /**
     * Get error property
     *
     * @return boolean $error
     */
    public function hasStopped()
    {
        return $this->error === true;
    }

    /**
     * Get message_type property
     *
     * @return string $message_type
     */
    public function getMessageType()
    {
        return $this->message_type;
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
     * Get message_params property
     *
     * @return string $message_params
     */
    public function getMessageParams()
    {
        return $this->message_params;
    }
}