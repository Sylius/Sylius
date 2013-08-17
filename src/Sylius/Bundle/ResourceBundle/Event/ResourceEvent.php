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
    const TYPE_SUCCESS  = 'success';
    const TYPE_ERROR    = 'error';
    const TYPE_INFO     = 'info';
    const TYPE_WARNING  = 'warning';

    protected $allowed_types = array(self::TYPE_SUCCESS, self::TYPE_ERROR, self::TYPE_INFO, self::TYPE_WARNING);

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
     * Get error property
     *
     * @return boolean $error
     */
    public function hasError()
    {
        return $this->error;
    }

    /**
     * Set error property
     *
     * @return boolean $error
     */
    public function setError($error)
    {
        return $this->error = $error;
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
     * Set message_type property
     *
     * @return string $message_type
     */
    public function setMessageType($message_type)
    {
        if(!in_array($message_type, $this->allowed_types)) {
            throw new \InvalidArgumentException('Allowed message types are \''.implode(', ', $this->allowed_types).'\'');
        }

        return $this->message_type = $message_type;
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
     * Set message property
     *
     * @return string $message
     */
    public function setMessage($message)
    {
        return $this->message = $message;
    }
}