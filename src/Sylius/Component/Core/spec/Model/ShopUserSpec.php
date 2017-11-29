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

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ShopUserInterface;

final class ShopUserSpec extends ObjectBehavior
{
    public function it_implements_user_component_interface(): void
    {
        $this->shouldImplement(ShopUserInterface::class);
    }
}
