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

namespace spec\Sylius\Component\Resource\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class FactorySpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith(\stdClass::class);
    }

    public function it_implements_factory_interface(): void
    {
        $this->shouldHaveType(FactoryInterface::class);
    }

    public function it_creates_a_new_instance_of_a_resource(): void
    {
        $this->createNew()->shouldHaveType(\stdClass::class);
    }
}
