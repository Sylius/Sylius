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

namespace spec\Sylius\Bundle\PayumBundle\Validator\GroupsGenerator;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Symfony\Component\Form\FormInterface;

final class GatewayConfigGroupsGeneratorSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith([
            'paypal_express_checkout' => ['paypal_express_checkout', 'sylius'],
            'stripe_checkout' => ['stripe_checkout', 'sylius'],
        ]);
    }

    function it_throws_error_if_invalid_object_is_passed(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [new \stdClass()])
        ;
    }

    function it_returns_gateway_config_validation_groups(
        GatewayConfigInterface $gatewayConfig,
        PaymentMethodInterface $paymentMethod,
    ): void {
        $paymentMethod->getGatewayConfig()->willReturn($gatewayConfig);
        $gatewayConfig->getFactoryName()->willReturn('paypal_express_checkout');

        $this($paymentMethod)->shouldReturn(['paypal_express_checkout', 'sylius']);
    }

    function it_returns_default_validation_groups_if_gateway_config_is_null(
        PaymentMethodInterface $paymentMethod,
    ): void {
        $paymentMethod->getGatewayConfig()->willReturn(null);

        $this($paymentMethod)->shouldReturn(['sylius']);
    }

    function it_returns_gateway_config_validation_groups_if_it_is_payment_method_form(
        FormInterface $form,
        GatewayConfigInterface $gatewayConfig,
        PaymentMethodInterface $paymentMethod,
    ): void {
        $form->getData()->willReturn($paymentMethod);
        $paymentMethod->getGatewayConfig()->willReturn($gatewayConfig);
        $gatewayConfig->getFactoryName()->willReturn('paypal_express_checkout');

        $this($form)->shouldReturn(['paypal_express_checkout', 'sylius']);
    }
}
