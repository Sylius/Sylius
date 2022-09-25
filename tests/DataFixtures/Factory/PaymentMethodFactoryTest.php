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
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\LocaleFactory;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\PaymentMethodFactory;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class PaymentMethodFactoryTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    function it_creates_payment_method_with_default_values(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        ChannelFactory::createMany(3);
        $paymentMethod = PaymentMethodFactory::createOne();

        $this->assertInstanceOf(PaymentMethodInterface::class, $paymentMethod->object());
        $this->assertNotNull($paymentMethod->getCode());
        $this->assertNotNull($paymentMethod->getName());
        $this->assertNotNull($paymentMethod->getDescription());
    }

    /** @test */
    function it_creates_payment_method_with_given_code(): void
    {
        $paymentMethod = PaymentMethodFactory::new()->withCode('PM2')->create();

        $this->assertEquals('PM2', $paymentMethod->getCode());
    }

    /** @test */
    function it_creates_payment_method_with_given_name(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $paymentMethod = PaymentMethodFactory::new()->withName('Payment method 2')->create();

        $this->assertEquals('Payment method 2', $paymentMethod->getName());
    }

    /** @test */
    function it_creates_payment_method_with_given_description(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $paymentMethod = PaymentMethodFactory::new()->withDescription('Credit card')->create();

        $this->assertEquals('Credit card', $paymentMethod->getDescription());
    }
}
