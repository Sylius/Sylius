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

namespace spec\Sylius\Bundle\ApiBundle\StateProvider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItem;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class RecursiveAdjustmentsStateProviderSpec extends ObjectBehavior
{
    private const IDENTIFIER = 'id';

    function let(RepositoryInterface $repository): void
    {
        $repository->getClassName()->willReturn(OrderItem::class);

        $this->beConstructedWith($repository, self::IDENTIFIER);
    }

    function it_is_a_state_provider(): void
    {
        $this->shouldImplement(ProviderInterface::class);
    }

    function it_throw_logic_exception_when_repository_is_for_not_a_not_recursive_adjustments_aware_resource(
        RepositoryInterface $repository,
    ): void {
        $repository->getClassName()->willReturn(\stdClass::class);

        $this->shouldThrow(\LogicException::class)->duringInstantiation();
    }

    function it_throws_exception_when_identifier_is_missing_from_uri_variables(
        RepositoryInterface $repository,
        Operation $operation,
    ): void {
        $repository->findOneBy(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(\RuntimeException::class)
            ->during('provide', [$operation, []])
        ;
    }

    function it_throws_exception_when_resource_cannot_be_found(
        RepositoryInterface $repository,
        Operation $operation,
    ): void {
        $repository->findOneBy([self::IDENTIFIER => 1])->willReturn(null);

        $this
            ->shouldThrow(\RuntimeException::class)
            ->during('provide', [$operation, [self::IDENTIFIER => 1]])
        ;
    }

    function it_returns_adjustments_recursively(
        RepositoryInterface $repository,
        Operation $operation,
        OrderItem $orderItem,
        AdjustmentInterface $firstAdjustment,
        AdjustmentInterface $secondAdjustment,
    ): void {
        $adjustments = new ArrayCollection([
            $firstAdjustment->getWrappedObject(),
            $secondAdjustment->getWrappedObject(),
        ]);

        $orderItem->getAdjustmentsRecursively()->willReturn($adjustments);
        $repository->findOneBy([self::IDENTIFIER => 1])->willReturn($orderItem);

        $this->provide($operation, [self::IDENTIFIER => 1])->shouldReturn($adjustments);
    }
}
