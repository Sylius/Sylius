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

namespace spec\Sylius\Bundle\PayumBundle\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;

final class GatewayConfigSpec extends ObjectBehavior
{
    function it_implements_payum_gateway_config_interface(): void
    {
        $this->shouldImplement(GatewayConfigInterface::class);
    }
}
