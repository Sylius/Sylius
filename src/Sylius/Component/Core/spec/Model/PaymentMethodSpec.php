<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Payum\Core\Model\GatewayConfigInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\PaymentMethod;
use Sylius\Component\Core\Model\PaymentMethodInterface;
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

    function it_is_payment_method()
    {
        $this->shouldHaveType(BasePaymentMethod::class);
    }

    function it_implements_payment_method_interface()
    {
        $this->shouldImplement(PaymentMethodInterface::class);
    }

    function it_has_channels_collection(ChannelInterface $firstChannel, ChannelInterface $secondChannel)
    {
        $this->addChannel($firstChannel);
        $this->addChannel($secondChannel);

        $this->getChannels()->shouldBeSameAs(new ArrayCollection([$firstChannel, $secondChannel]));
    }

    function it_can_add_and_remove_channels(ChannelInterface $channel)
    {
        $this->addChannel($channel);
        $this->hasChannel($channel)->shouldReturn(true);

        $this->removeChannel($channel);
        $this->hasChannel($channel)->shouldReturn(false);
    }

    function its_gateway_config_is_mutable(GatewayConfigInterface $gatewayConfig)
    {
        $this->setGatewayConfig($gatewayConfig);
        $this->getGatewayConfig()->shouldReturn($gatewayConfig);
    }

    function it_returns_name_from_gateway_config_while_getting_a_gateway(GatewayConfigInterface $gatewayConfig)
    {
        $this->getGateway()->shouldReturn(null);

        $gatewayConfig->getGatewayName()->willReturn('gateway');
        $this->setGatewayConfig($gatewayConfig);

        $this->getGateway()->shouldReturn('gateway');
    }

    public function getMatchers()
    {
        return [
            'beSameAs' => function ($subject, $key) {
                if (!$subject instanceof Collection || !$key instanceof Collection) {
                    return false;
                }

                for ($i = 0; $i < $subject->count(); $i++) {
                    if ($subject->get($i) !== $key->get($i)->getWrappedObject()) {
                        return false;
                    }
                }

                return true;
            },
        ];
    }
}
