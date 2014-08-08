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

use Symfony\Component\EventDispatcher\Event;

/**
 * Flash message event.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class FlashEvent extends Event
{
    /**
     * Flash message
     *
     * @var string
     */
    protected $message;

    /**
     * @param null|string $message
     */
    public function __construct($message = null)
    {
        $this->message = $message;
    }

    /**
     * Get flash message.
     *
     * @return null|string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set flash message.
     *
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }
}
