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

namespace Sylius\Tests\DataFixtures\Factory;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ChannelFactory;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CountryFactory;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CustomerFactory;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\OrderFactory;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ShopUserFactory;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class OrderFactoryTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    function it_creates_order_with_default_values(): void
    {
        ChannelFactory::createOne();
        $order = OrderFactory::createOne();

        $this->assertInstanceOf(OrderInterface::class, $order->object());
        $this->assertNotNull($order->getChannel());
        $this->assertNotNull($order->getCurrencyCode());
        $this->assertNotNull($order->getLocaleCode());
    }

    /** @test */
    function it_creates_order_with_given_channel_as_proxy(): void
    {
        $channel = ChannelFactory::createOne();
        $order = OrderFactory::new()->withChannel($channel)->create();

        $this->assertEquals($channel->object(), $order->getChannel());
    }

    /** @test */
    function it_creates_order_with_given_channel(): void
    {
        $channel = ChannelFactory::createOne()->object();
        $order = OrderFactory::new()->withChannel($channel)->create();

        $this->assertEquals($channel, $order->getChannel());
    }

    /** @test */
    function it_creates_order_with_given_channel_as_string(): void
    {
        $order = OrderFactory::new()->withChannel('default')->create();

        $this->assertEquals('default', $order->getChannel()->getCode());
    }

    /** @test */
    function it_creates_order_with_given_customer_as_proxy(): void
    {
        $customer = CustomerFactory::createOne();
        $order = OrderFactory::new()->withCustomer($customer)->create();

        $this->assertEquals($customer->object(), $order->getCustomer());
    }

    /** @test */
    function it_creates_order_with_given_customer(): void
    {
        $customer = CustomerFactory::createOne()->object();
        $order = OrderFactory::new()->withCustomer($customer)->create();

        $this->assertEquals($customer, $order->getCustomer());
    }

    /** @test */
    function it_creates_order_with_given_customer_as_string(): void
    {
        $order = OrderFactory::new()->withCustomer('john.doe@example.com')->create();

        $this->assertEquals('john.doe@example.com', $order->getCustomer()->getEmail());
    }

    /** @test */
    function it_creates_order_with_given_country_as_proxy(): void
    {
        $country = CountryFactory::new()->withCode('FR')->create();
        $order = OrderFactory::new()->withCountry($country)->create();

        $this->assertEquals('FR', $order->getBillingAddress()?->getCountryCode());
    }

    /** @test */
    function it_creates_order_with_given_country(): void
    {
        $country = CountryFactory::new()->withCode('FR')->create()->object();
        $order = OrderFactory::new()->withCountry($country)->create();

        $this->assertEquals('FR', $order->getBillingAddress()?->getCountryCode());
    }

    /** @test */
    function it_creates_order_with_given_country_as_string(): void
    {
        $order = OrderFactory::new()->withCountry('FR')->create();

        $this->assertEquals('FR', $order->getBillingAddress()?->getCountryCode());
    }
}
