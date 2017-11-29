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

namespace spec\Sylius\Component\Core\Model;

use Payum\Core\Model\GatewayConfigInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentMethod as BasePaymentMethod;

final class PaymentMethodSpec extends ObjectBehavior
{
    public function it_is_payment_method(): void
    {
        $this->shouldHaveType(BasePaymentMethod::class);
    }

    public function it_implements_payment_method_interface(): void
    {
        $this->shouldImplement(PaymentMethodInterface::class);
    }

    public function it_has_channels_collection(ChannelInterface $firstChannel, ChannelInterface $secondChannel): void
    {
        $this->addChannel($firstChannel);
        $this->addChannel($secondChannel);

        $this->getChannels()->shouldIterateAs([$firstChannel, $secondChannel]);
    }

    public function it_can_add_and_remove_channels(ChannelInterface $channel): void
    {
        $this->addChannel($channel);
        $this->hasChannel($channel)->shouldReturn(true);

        $this->removeChannel($channel);
        $this->hasChannel($channel)->shouldReturn(false);
    }

    public function its_gateway_config_is_mutable(GatewayConfigInterface $gatewayConfig): void
    {
        $this->setGatewayConfig($gatewayConfig);
        $this->getGatewayConfig()->shouldReturn($gatewayConfig);
    }
}
