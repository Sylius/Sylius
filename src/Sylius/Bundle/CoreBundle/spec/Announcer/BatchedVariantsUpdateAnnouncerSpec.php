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

namespace spec\Sylius\Bundle\CoreBundle\Announcer;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Announcer\BatchedVariantsUpdateAnnouncerInterface;
use Sylius\Component\Product\Command\UpdateBatchedVariants;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;


final class BatchedVariantsUpdateAnnouncerSpec extends ObjectBehavior
{
    function let(MessageBusInterface $eventBus): void
    {
        $this->beConstructedWith($eventBus, 1);
    }

    function it_implements_catalog_promotion_announcer_interface(): void
    {
        $this->shouldImplement(BatchedVariantsUpdateAnnouncerInterface::class);
    }

    function it_dispatches_command_with_batched_variants(
        MessageBusInterface $eventBus
    ): void {

        $command1 = new UpdateBatchedVariants(['first_code']);
        $command2 = new UpdateBatchedVariants(['second_code']);
        $eventBus->dispatch($command1)->willReturn(new Envelope($command1))->shouldBeCalled();
        $eventBus->dispatch($command2)->willReturn(new Envelope($command2))->shouldBeCalled();

        $this->dispatchVariantsUpdateCommand(['first_code', 'second_code']);
    }
}
