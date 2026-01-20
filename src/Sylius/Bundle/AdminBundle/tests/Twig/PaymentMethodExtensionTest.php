<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\Bundle\AdminBundle\Twig;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AdminBundle\Twig\PaymentMethodExtension;

final class PaymentMethodExtensionTest extends TestCase
{
    #[Test]
    public function it_gets_payment_gateways(): void
    {
        $gatewayFactories = ['offline' => 'Offline', 'stripe' => 'Stripe'];
        $excludedGatewayFactories = ['offline'];

        $paymentMethodExtension = new PaymentMethodExtension($gatewayFactories, $excludedGatewayFactories);

        $paymentGateways = $paymentMethodExtension->getPaymentGateways();

        $this->assertArrayHasKey('stripe', $paymentGateways);
        $this->assertArrayNotHasKey('offline', $paymentGateways);
    }
}
