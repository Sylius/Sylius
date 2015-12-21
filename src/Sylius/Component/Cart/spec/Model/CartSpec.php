<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Cart\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Order\Model\Order;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CartSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Cart\Model\Cart');
    }

    function it_implements_Sylius_cart_interface()
    {
        $this->shouldImplement(CartInterface::class);
    }

    function it_extends_Sylius_order()
    {
        $this->shouldHaveType(Order::class);
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
