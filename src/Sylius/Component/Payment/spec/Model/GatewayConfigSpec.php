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

namespace spec\Sylius\Component\Payment\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Payment\Model\GatewayConfigInterface;

final class GatewayConfigSpec extends ObjectBehavior
{
    function it_implements_gateway_config_interface(): void
    {
        $this->shouldImplement(GatewayConfigInterface::class);
    }

    function its_factory_name_is_mutable(): void
    {
        $this->setFactoryName('Offline');
        $this->getFactoryName()->shouldReturn('Offline');
    }

    function its_gateway_name_is_mutable(): void
    {
        $this->setGatewayName('Offline');
        $this->getGatewayName()->shouldReturn('Offline');
    }

    function its_config_s_mutable(): void
    {
        $this->setConfig(['key' => '123']);
        $this->getConfig()->shouldReturn(['key' => '123']);
    }
}
