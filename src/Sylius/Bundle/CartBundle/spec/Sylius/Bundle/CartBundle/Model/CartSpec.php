<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CartBundle\Model;

use PhpSpec\ObjectBehavior;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CartSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Model\Cart');
    }

    function it_implements_Sylius_cart_interface()
    {
        $this->shouldImplement('Sylius\Bundle\CartBundle\Model\CartInterface');
    }

    function it_extends_Sylius_order()
    {
        $this->shouldHaveType('Sylius\Bundle\OrderBundle\Model\Order');
    }

    function it_is_not_expired_by_default()
    {
        $this->shouldNotBeExpired();
    }

    function it_is_not_expired_if_the_expiration_time_is_in_future()
    {
        $expiresAt = new \DateTime('tomorrow');
        $this->setExpiresAt($expiresAt);

        $this->shouldNotBeExpired();
    }

    function it_is_expired_if_the_expiration_time_is_in_past()
    {
        $expiresAt = new \DateTime('-1 hour');
        $this->setExpiresAt($expiresAt);

        $this->shouldBeExpired();
    }
}
