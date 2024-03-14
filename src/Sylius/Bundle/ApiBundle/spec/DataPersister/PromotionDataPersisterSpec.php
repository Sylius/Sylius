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

namespace spec\Sylius\Bundle\ApiBundle\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Exception\PromotionCannotBeRemoved;
use Sylius\Component\Core\Model\PromotionInterface;

final class PromotionDataPersisterSpec extends ObjectBehavior
{
    function let(ContextAwareDataPersisterInterface $dataPersister): void
    {
        $this->beConstructedWith($dataPersister);
    }

    function it_is_a_context_aware_persister(): void
    {
        $this->shouldImplement(ContextAwareDataPersisterInterface::class);
    }

    function it_supports_only_promotion(PromotionInterface $promotion): void
    {
        $this->supports(new \stdClass())->shouldReturn(false);
        $this->supports($promotion)->shouldReturn(true);
    }

    function it_uses_inner_persister_to_persist_promotion(
        ContextAwareDataPersisterInterface $dataPersister,
        PromotionInterface $promotion,
    ): void {
        $dataPersister->persist($promotion, [])->shouldBeCalled();

        $this->persist($promotion);
    }

    function it_throws_cannot_be_removed_exception_if_constraint_fails_on_removal(
        ContextAwareDataPersisterInterface $dataPersister,
        PromotionInterface $promotion,
    ): void {
        $dataPersister->remove($promotion, [])->willThrow(ForeignKeyConstraintViolationException::class);

        $this->shouldThrow(PromotionCannotBeRemoved::class)->during('remove', [$promotion]);
    }

    function it_uses_inner_persister_to_remove_promotion(
        ContextAwareDataPersisterInterface $dataPersister,
        PromotionInterface $promotion,
    ): void {
        $dataPersister->remove($promotion, [])->shouldBeCalled();

        $this->remove($promotion);
    }
}
