<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\Address;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Currency\Model\Currency;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class OrderFactorySpec extends ObjectBehavior
{
    public function let(FactoryInterface $baseFactory): void
    {
        $this->beConstructedWith($baseFactory);
    }

    public function it_creates_an_order(FactoryInterface $baseFactory): void
    {
        $someOrder = new Order();
        $baseFactory->createNew()->willReturn($someOrder);

        $this->createNew()->shouldReturn($someOrder);
    }

    public function it_creates_a_cart(FactoryInterface $baseFactory): void {
        $someOrder = new Order();
        $baseFactory->createNew()->willReturn($someOrder);

        $defaultAddress = new Address();
        $defaultAddress->setStreet('123 Main St');

        $customer = new Customer();
        $customer->setDefaultAddress($defaultAddress);

        $currency = new Currency();
        $currency->setCode('USD');

        $channel = new Channel();
        $channel->setBaseCurrency($currency);

        $cart = $this->createNewCart(
            $channel,
            $customer,
            'en_US',
            'mytoken',
        );

        $cart->getState()->shouldReturn(Order::STATE_CART);
        $cart->getChannel()->shouldReturn($channel);
        $cart->getLocaleCode()->shouldReturn('en_US');
        $cart->getCurrencyCode()->shouldReturn('USD');
        $cart->getTokenValue()->shouldReturn('mytoken');
        $cart->getCustomer()->shouldReturn($customer);
        $cart->getBillingAddress()->getStreet()->shouldReturn('123 Main St');
    }
}
