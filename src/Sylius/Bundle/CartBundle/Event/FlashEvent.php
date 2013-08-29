<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Flash message event.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class FlashEvent extends Event
{
    /**
     * @var string
     */
    private $message;

    /**
     * @param string $message
     */
    public function __construct($message = null)
    {
        $this->message = $message;
    }

    /**
     * @return null|string
     */
    public function getMessage()
    {
        return $this->message;
    }
}
