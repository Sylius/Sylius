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

namespace spec\Sylius\Bundle\ApiBundle\DataTransformer;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Cart\PickupCart;
use Sylius\Bundle\ApiBundle\Command\CommandAwareDataTransformerInterface;
use Sylius\Bundle\ApiBundle\DataTransformer\CommandDataTransformerInterface;
use Sylius\Component\Core\Model\Order;

final class CommandAwareInputDataTransformerSpec extends ObjectBehavior
{
    private static array $CONTEXT = ['input' => ['class' => CommandAwareDataTransformerInterface::class]];

    function let(CommandDataTransformerInterface $commandDataTransformer): void
    {
        $this->beConstructedWith($commandDataTransformer);
    }

    function it_transforms_object_by_proper_data_transformer(CommandDataTransformerInterface $commandDataTransformer): void
    {
        $object = new PickupCart();

        $commandDataTransformer->supportsTransformation($object)->willReturn(true);
        $commandDataTransformer
            ->transform($object, CommandAwareDataTransformerInterface::class, $this::$CONTEXT)
            ->willReturn($object)
        ;

        $this
            ->transform(
                $object,
                CommandAwareDataTransformerInterface::class,
                $this::$CONTEXT,
            )
            ->shouldReturn($object)
        ;
    }

    function it_supports_only_command_aware_data_transformer_type(): void
    {
        $this
            ->supportsTransformation(new PickupCart(), CommandAwareDataTransformerInterface::class, $this::$CONTEXT)
            ->shouldReturn(true)
        ;

        $this
            ->supportsTransformation(
                new PickupCart(),
                CommandAwareDataTransformerInterface::class,
                ['input' => ['class' => Order::class]],
            )
            ->shouldReturn(false)
        ;
    }

    function it_supports_only_command_aware_data_transformer_type_and_allow_null_input_context_key(): void
    {
        $this
            ->supportsTransformation(new PickupCart(), CommandAwareDataTransformerInterface::class, [])
            ->shouldReturn(false)
        ;
    }
}
