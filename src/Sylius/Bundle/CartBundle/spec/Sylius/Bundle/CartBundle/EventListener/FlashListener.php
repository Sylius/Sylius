<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CartBundle\EventListener;

use PHPSpec2\ObjectBehavior;

/**
 * Flash message listener spec.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class FlashListener extends ObjectBehavior
{
    /**
     * @param Symfony\Component\HttpFoundation\Session\SessionInterface $session
     */
    function let($session)
    {
        $this->beConstructedWith($session);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\EventListener\FlashListener');
    }
}
