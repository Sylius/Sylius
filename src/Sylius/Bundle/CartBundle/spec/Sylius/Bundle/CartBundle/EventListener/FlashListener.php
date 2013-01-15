<?php

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

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\EventListener\FlashListener');
    }
}
