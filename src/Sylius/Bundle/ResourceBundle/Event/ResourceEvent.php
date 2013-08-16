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

use Symfony\Component\EventDispatcher;

/**
 * Resource event.
 *
 * @author Jérémy Leherpeur <jeremy@lehepeur.net>
 */
class ResourceEvent extends GenericEvent
{
    /**
     * Indicate if an error has been detected
     *
     * @var boolean
     */
    protected $error = false;

    /**
     * Error message
     *
     * @var string
     */
    protected $error_message = '';

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
     * Get error_message property
     *
     * @return string $error_message
     */
    public function getErrorMessage()
    {
        return $this->error_message;
    }

    /**
     * Set error_message property
     *
     * @return string $error_message
     */
    public function setErrorMessage($error_message)
    {
        return $this->error_message = $error_message;
    }
}
