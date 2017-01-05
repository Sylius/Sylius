<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PayumBundle\Model;

use Payum\Core\Model\GatewayConfigInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PayumBundle\Model\PaymentMethod;
use Sylius\Bundle\PayumBundle\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentMethod as BasePaymentMethod;
use Sylius\Component\Resource\Exception\UnsupportedMethodException;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class PaymentMethodSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PaymentMethod::class);
    }

    function it_implements_payment_method_interface()
    {
        $this->shouldImplement(PaymentMethodInterface::class);
    }

    function it_is_base_payment_method()
    {
        $this->shouldHaveType(BasePaymentMethod::class);
    }

    function its_gateway_is_gateway_config_object(GatewayConfigInterface $gatewayConfig)
    {
        $this->setGatewayConfig($gatewayConfig);
        $this->getGatewayConfig()->shouldReturn($gatewayConfig);
    }

    function it_does_not_support_set_gateway_method()
    {
        $this->shouldThrow(UnsupportedMethodException::class)->during('setGateway', ['gateway']);
    }

    function it_returns_name_from_gateway_config_while_getting_a_gateway(GatewayConfigInterface $gatewayConfig)
    {
        $this->getGateway()->shouldReturn(null);

        $gatewayConfig->getGatewayName()->willReturn('gateway');
        $this->setGatewayConfig($gatewayConfig);

        $this->getGateway()->shouldReturn('gateway');
    }
}
