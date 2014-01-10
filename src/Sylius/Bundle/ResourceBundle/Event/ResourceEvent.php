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

<<<<<<< HEAD
    public function stop($message, $type = self::TYPE_ERROR, $params = array())
=======
    public function stop($message, $type = self::TYPE_ERROR, $parameters = array())
>>>>>>> Introduce domain manager and restore the ability to cancel action
    {
        $this->error = true;
        $this->messageType = $type;
        $this->message = $message;
<<<<<<< HEAD
        $this->messageParams = $params;
=======
        $this->messageParameters = $parameters;

>>>>>>> Introduce domain manager and restore the ability to cancel action
        $this->stopPropagation();
    }

    /**
     * Indicate if an error has been detected
     *
<<<<<<< HEAD
     * @var Boolean
=======
     * @var boolean
>>>>>>> Introduce domain manager and restore the ability to cancel action
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
<<<<<<< HEAD
    protected $messageParams = array();
=======
    protected $messageParameters = array();
>>>>>>> Introduce domain manager and restore the ability to cancel action

    /**
     * Get error property
     *
<<<<<<< HEAD
     * @return Boolean $error
=======
     * @return boolean $error
>>>>>>> Introduce domain manager and restore the ability to cancel action
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
<<<<<<< HEAD
     * Get messageParams property
     *
     * @return string $messageParams
     */
    public function getMessageParams()
    {
        return $this->messageParams;
=======
     * Get messageParameters property
     *
     * @return string $messageParameters
     */
    public function getMessageParameters()
    {
        return $this->messageParameters;
>>>>>>> Introduce domain manager and restore the ability to cancel action
    }
}
