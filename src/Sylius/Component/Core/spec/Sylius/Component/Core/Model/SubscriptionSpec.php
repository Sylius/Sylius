<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\UserInterface;

class SubscriptionSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Model\Subscription');
    }

    public function it_implements_Sylius_core_subscription_interface()
    {
        $this->shouldImplement('Sylius\Component\Core\Model\SubscriptionInterface');
    }

    public function it_extends_Sylius_subscription_bundle_subscription()
    {
        $this->shouldHaveType('Sylius\Component\Subscription\Model\Subscription');
    }

    public function it_has_no_user_by_default()
    {
        $this->getUser()->shouldReturn(null);
    }

    public function its_user_is_mutable(UserInterface $user)
    {
        $this->setUser($user);
        $this->getUser()->shouldReturn($user);
    }

    public function is_has_no_shipping_address_by_default()
    {
        $this->getShippingAddress()->shouldReturn(null);
    }

    public function its_shipping_address_is_mutable(AddressInterface $address)
    {
        $this->setShippingAddress($address);
        $this->getShippingAddress()->shouldReturn($address);
    }
}
