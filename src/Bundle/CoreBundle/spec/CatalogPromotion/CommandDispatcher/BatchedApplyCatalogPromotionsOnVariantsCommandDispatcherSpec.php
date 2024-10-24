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

namespace spec\Sylius\Bundle\CoreBundle\CatalogPromotion\CommandDispatcher;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\ApplyCatalogPromotionsOnVariants;
use Sylius\Bundle\CoreBundle\CatalogPromotion\CommandDispatcher\ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class BatchedApplyCatalogPromotionsOnVariantsCommandDispatcherSpec extends ObjectBehavior
{
    public function let(MessageBusInterface $messageBus): void
    {
        $this->beConstructedWith($messageBus, 2);
    }

    function it_implements_apply_catalog_promotions_on_variants_command_dispatcher_interface(): void
    {
        $this->shouldImplement(ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface::class);
    }

    public function it_dispatches_in_two_batches(MessageBusInterface $messageBus): void
    {
        $firstCommand = new ApplyCatalogPromotionsOnVariants(['example_variant1', 'example_variant2']);
        $secondCommand = new ApplyCatalogPromotionsOnVariants(['example_variant3']);

        $messageBus->dispatch($firstCommand)->willReturn(new Envelope($firstCommand))->shouldBeCalledOnce();
        $messageBus->dispatch($secondCommand)->willReturn(new Envelope($secondCommand))->shouldBeCalledOnce();

        $this->updateVariants(['example_variant1', 'example_variant2', 'example_variant3']);
    }

    public function it_dispatches_in_one_batch(MessageBusInterface $messageBus): void
    {
        $command = new ApplyCatalogPromotionsOnVariants(['example_variant1', 'example_variant2']);

        $messageBus->dispatch($command)->willReturn(new Envelope($command))->shouldBeCalledOnce();

        $this->updateVariants(['example_variant1', 'example_variant2']);
    }
}
