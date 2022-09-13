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
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ShopBillingDataFactory;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ZoneFactory;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class ChannelFactoryTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    function it_creates_channel(): void
    {
        $channel = ChannelFactory::createOne();

        $this->assertInstanceOf(ChannelInterface::class, $channel->object());
        $this->assertNotNull($channel->getCode());
        $this->assertNotNull($channel->getName());
        $this->assertNotNull($channel->getColor());
        $this->assertIsBool($channel->isEnabled());
        $this->assertFalse($channel->isSkippingShippingStepAllowed());
        $this->assertFalse($channel->isSkippingPaymentStepAllowed());
        $this->assertTrue($channel->isAccountVerificationRequired());
        $this->assertNotNull($channel->getDefaultTaxZone());
        $this->assertEquals('order_items_based', $channel->getTaxCalculationStrategy());
        $this->assertNotNull($channel->getDefaultLocale());
        $this->assertNotNull($channel->getBaseCurrency());
        $this->assertNull($channel->getThemeName());
        $this->assertNull($channel->getContactEmail());
        $this->assertNull($channel->getContactPhoneNumber());
        $this->assertNull($channel->getShopBillingData());
        $this->assertNotNull($channel->getMenuTaxon());
    }

    /** @test */
    function it_creates_channel_with_given_code(): void
    {
        $channel = ChannelFactory::new()->withCode('default_channel')->create();

        $this->assertEquals('default_channel', $channel->getCode());
        $this->assertEquals('default_channel.localhost', $channel->getHostname());
    }

    /** @test */
    function it_creates_channel_with_given_name(): void
    {
        $channel = ChannelFactory::new()->withName('Default channel')->create();

        $this->assertEquals('Default channel', $channel->getName());
        $this->assertEquals('Default_channel', $channel->getCode());
    }

    /** @test */
    function it_creates_channel_with_given_hostname(): void
    {
        $channel = ChannelFactory::new()->withHostname('default.localhost')->create();

        $this->assertEquals('default.localhost', $channel->getHostname());
    }

    /** @test */
    function it_creates_channel_with_given_color(): void
    {
        $channel = ChannelFactory::new()->withColor('#1abb9c')->create();

        $this->assertEquals('#1abb9c', $channel->getColor());
    }

    /** @test */
    function it_creates_enabled_channel(): void
    {
        $channel = ChannelFactory::new()->enabled()->create();

        $this->assertTrue($channel->isEnabled());
    }

    /** @test */
    function it_creates_disabled_channel(): void
    {
        $channel = ChannelFactory::new()->disabled()->create();

        $this->assertFalse($channel->isEnabled());
    }

    /** @test */
    function it_creates_channel_with_skipping_shipping_step_allowed(): void
    {
        $channel = ChannelFactory::new()->withSkippingShippingStepAllowed()->create();

        $this->assertTrue($channel->isSkippingShippingStepAllowed());
    }

    /** @test */
    function it_creates_channel_with_skipping_payment_step_allowed(): void
    {
        $channel = ChannelFactory::new()->withSkippingPaymentStepAllowed()->create();

        $this->assertTrue($channel->isSkippingPaymentStepAllowed());
    }

    /** @test */
    function it_creates_channel_without_account_verification_required(): void
    {
        $channel = ChannelFactory::new()->withoutAccountVerificationRequired()->create();

        $this->assertFalse($channel->isAccountVerificationRequired());
    }

    /** @test */
    function it_creates_channel_with_given_already_existing_proxy_default_tax_zone(): void
    {
        $zone = ZoneFactory::new()->withCode('world')->create();

        $channel = ChannelFactory::new()->withDefaultTaxZone($zone)->create();

        $this->assertEquals('world', $channel->getDefaultTaxZone()->getCode());
    }

    /** @test */
    function it_creates_channel_with_given_already_existing_default_tax_zone(): void
    {
        $zone = ZoneFactory::new()->withCode('world')->create()->object();

        $channel = ChannelFactory::new()->withDefaultTaxZone($zone)->create();

        $this->assertEquals('world', $channel->getDefaultTaxZone()->getCode());
    }

    /** @test */
    function it_creates_channel_with_given_already_existing_default_tax_zone_code(): void
    {
        ZoneFactory::new()->withCode('world')->create();

        $channel = ChannelFactory::new()->withDefaultTaxZone('world')->create();

        $this->assertEquals('world', $channel->getDefaultTaxZone()->getCode());
    }

    /** @test */
    function it_creates_channel_with_given_tax_calculation_strategy(): void
    {
        $channel = ChannelFactory::new()->withTaxCalculationStrategy('order_based')->create();

        $this->assertEquals('order_based', $channel->getTaxCalculationStrategy());
    }

    /** @test */
    function it_creates_channel_with_given_theme_name(): void
    {
        $channel = ChannelFactory::new()->withThemeName('custom_theme')->create();

        $this->assertEquals('custom_theme', $channel->getThemeName());
    }

    /** @test */
    function it_creates_channel_with_given_contact_email(): void
    {
        $channel = ChannelFactory::new()->withContactEmail('darthvader@starwars.com')->create();

        $this->assertEquals('darthvader@starwars.com', $channel->getContactEmail());
    }

    /** @test */
    function it_creates_channel_with_given_contact_phone_number(): void
    {
        $channel = ChannelFactory::new()->withContactPhoneNumber('0666-0666')->create();

        $this->assertEquals('0666-0666', $channel->getContactPhoneNumber());
    }

    /** @test */
    function it_creates_channel_with_given_array_of_shop_billing_data(): void
    {
        $channel = ChannelFactory::new()->withShopBillingData(['company' => 'Sylius'])->create();

        $this->assertEquals('Sylius', $channel->getShopBillingData()->getCompany());
    }

    /** @test */
    function it_creates_channel_with_given_proxy_shop_billing_data(): void
    {
        $shopBillingData = ShopBillingDataFactory::new()->withCompany('Sylius')->create();

        $channel = ChannelFactory::new()->withShopBillingData($shopBillingData)->create();

        $this->assertEquals('Sylius', $channel->getShopBillingData()->getCompany());
    }

    /** @test */
    function it_creates_channel_with_given_shop_billing_data(): void
    {
        $shopBillingData = ShopBillingDataFactory::new()->withCompany('Sylius')->create()->object();

        $channel = ChannelFactory::new()->withShopBillingData($shopBillingData)->create();

        $this->assertEquals('Sylius', $channel->getShopBillingData()->getCompany());
    }
}
