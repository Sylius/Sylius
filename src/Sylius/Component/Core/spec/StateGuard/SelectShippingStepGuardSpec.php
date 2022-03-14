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

namespace spec\Sylius\Component\Core\StateGuard;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\StateGuard\OrderGuardInterface;

class SelectShippingStepGuardSpec extends ObjectBehavior
{
    function it_implements_order_guard_interface()
    {
        $this->shouldImplement(OrderGuardInterface::class);
    }
}
