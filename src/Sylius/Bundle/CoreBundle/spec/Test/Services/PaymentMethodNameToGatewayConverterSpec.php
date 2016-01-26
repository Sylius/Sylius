<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace spec\Sylius\Bundle\CoreBundle\Test\Services;
 
use Payum\Core\GatewayInterface;
use Payum\Core\Registry\RegistryInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Test\Services\PaymentMethodNameToGatewayConverterInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class PaymentMethodNameToGatewayConverterSpec extends ObjectBehavior
{
    function let(RegistryInterface $payum)
    {
        $this->beConstructedWith($payum);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Test\Services\PaymentMethodNameToGatewayConverter');
    }

    function it_implements_payment_method_name_to_gateway_converter_interface()
    {
        $this->shouldImplement(PaymentMethodNameToGatewayConverterInterface::class);
    }

    function it_converts_payment_method_name_to_gateway($payum, GatewayInterface $offlineGateway, GatewayInterface $paypalCheckoutExpressGateway)
    {
        $gateways = [
            'offline' => $offlineGateway,
            'paypal_checkout_express' => $paypalCheckoutExpressGateway,
        ];
        $payum->getGateways()->willReturn($gateways);

        $this->convert('offline')->shouldReturn('offline');

        $this->convert('Paypal Checkout Express')->shouldReturn('paypal_checkout_express');
    }

    function it_throws_exception_when_cannot_map_gateway($payum, GatewayInterface $paypalCheckoutExpressGateway)
    {
        $gateways = [
            'paypal_checkout_express' => $paypalCheckoutExpressGateway,
        ];
        $payum->getGateways()->willReturn($gateways);

        $this->shouldThrow(new \RuntimeException('Cannot convert offline to gateway'))->during('convert', ['offline']);
    }

    function it_throws_exception_when_given_name_is_null()
    {
        $this->shouldThrow(new \InvalidArgumentException('Payment method name cannot be null'))->during('convert', ['']);
    }
}
