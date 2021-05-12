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

namespace spec\Sylius\Bundle\ApiBundle\Map;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Map\CommandItemIriArgumentToIdentifierMapInterface;

final class CommandItemIriArgumentToIdentifierMapSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(['Sylius\Bundle\ApiBundle\Command\Cart\PickupCart' => 'token']);
    }

    function it_is_command_item_iri_argument_to_identifier_map(): void
    {
        $this->shouldImplement(CommandItemIriArgumentToIdentifierMapInterface::class);
    }

    function it_gets_an_element(): void
    {
        $this->get('Sylius\Bundle\ApiBundle\Command\Cart\PickupCart')->shouldReturn('token');
    }

    function it_has_an_element(): void
    {
        $this->has('Sylius\Bundle\ApiBundle\Command\Cart\PickupCart')->shouldReturn(true);
    }

    function it_has_no_element(): void
    {
        $this->has('Sylius\Bundle\ApiBundle\Command\Cart\CreateCart')->shouldReturn(false);
    }
}
