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

namespace spec\Sylius\Bundle\CoreBundle\Commander;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Command\ApplyCatalogPromotionsOnVariants;
use Sylius\Bundle\CoreBundle\CommandDispatcher\ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class UpdateVariantsCommanderSpec extends ObjectBehavior
{
    function let(MessageBusInterface $eventBus): void
    {
        $this->beConstructedWith($eventBus, 1);
    }

    function it_implements_catalog_promotion_announcer_interface(): void
    {
        $this->shouldImplement(ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface::class);
    }

    function it_dispatches_command_with_batched_variants(MessageBusInterface $eventBus): void
    {
        $command1 = new ApplyCatalogPromotionsOnVariants(['first_code']);
        $command2 = new ApplyCatalogPromotionsOnVariants(['second_code']);
        $eventBus->dispatch($command1)->willReturn(new Envelope($command1))->shouldBeCalled();
        $eventBus->dispatch($command2)->willReturn(new Envelope($command2))->shouldBeCalled();

        $this->updateVariants(['first_code', 'second_code']);
    }
}
