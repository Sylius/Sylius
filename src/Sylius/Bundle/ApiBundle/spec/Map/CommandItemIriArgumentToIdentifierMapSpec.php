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
use Sylius\Bundle\ApiBundle\Map\CommandItemIriArgumentToIdentifierMap;
use Sylius\Bundle\ApiBundle\Map\CommandItemIriArgumentToIdentifierMapInterface;
use Webmozart\Assert\Assert;

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

    function it_get_element(): void
    {
        $map = new CommandItemIriArgumentToIdentifierMap(['Sylius\Bundle\ApiBundle\Command\Cart\PickupCart' => 'token']);
        Assert::same('token', $map->get('Sylius\Bundle\ApiBundle\Command\Cart\PickupCart'));
    }

    function it_has_element(): void
    {
        $map = new CommandItemIriArgumentToIdentifierMap(['Sylius\Bundle\ApiBundle\Command\Cart\PickupCart' => 'token']);
        Assert::true($map->has('Sylius\Bundle\ApiBundle\Command\Cart\PickupCart'));
    }

    function it_has_not_element(): void
    {
        $map = new CommandItemIriArgumentToIdentifierMap(['Sylius\Bundle\ApiBundle\Command\Cart\PickupCart' => 'token']);
        Assert::false($map->has('Sylius\Bundle\ApiBundle\Command\Cart\CreateCart'));
    }
}
