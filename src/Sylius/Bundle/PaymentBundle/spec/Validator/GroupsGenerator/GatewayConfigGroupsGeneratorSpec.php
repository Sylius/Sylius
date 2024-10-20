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

namespace spec\Sylius\Bundle\PaymentBundle\Validator\GroupsGenerator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Payment\Model\GatewayConfigInterface;

final class GatewayConfigGroupsGeneratorSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(['sylius'], [
            'paypal_express_checkout' => ['paypal_express_checkout', 'sylius'],
            'stripe_checkout' => ['stripe_checkout', 'sylius'],
        ]);
    }

    function it_returns_gateway_config_validation_groups(
        GatewayConfigInterface $gatewayConfig,
    ): void {
        $gatewayConfig->getFactoryName()->willReturn('paypal_express_checkout');

        $this($gatewayConfig)->shouldReturn(['paypal_express_checkout', 'sylius']);
    }

    function it_returns_default_validation_groups_if_factory_name_is_null(
        GatewayConfigInterface $gatewayConfig,
    ): void {
        $gatewayConfig->getFactoryName()->willReturn(null);

        $this($gatewayConfig)->shouldReturn(['sylius']);
    }
}
