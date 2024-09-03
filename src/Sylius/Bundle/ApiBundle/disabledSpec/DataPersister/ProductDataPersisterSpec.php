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
use Sylius\Bundle\ApiBundle\Exception\ProductCannotBeRemoved;
use Sylius\Component\Core\Model\ProductInterface;

final class ProductDataPersisterSpec extends ObjectBehavior
{
    function let(ContextAwareDataPersisterInterface $persister): void
    {
        $this->beConstructedWith($persister);
    }

    function it_is_a_context_aware_persister(): void
    {
        $this->shouldImplement(ContextAwareDataPersisterInterface::class);
    }

    function it_supports_only_product(ProductInterface $product): void
    {
        $this->supports(new \stdClass())->shouldReturn(false);
        $this->supports($product)->shouldReturn(true);
    }

    function it_uses_inner_persister_to_persist_a_product(
        ContextAwareDataPersisterInterface $persister,
        ProductInterface $product,
    ): void {
        $persister->persist($product, [])->shouldBeCalled();

        $this->persist($product, []);
    }

    function it_throws_cannot_be_removed_exception_if_constraint_fails_on_removal(
        ContextAwareDataPersisterInterface $persister,
        ProductInterface $product,
    ): void {
        $persister
            ->remove($product, [])
            ->willThrow(ForeignKeyConstraintViolationException::class)
        ;

        $this
            ->shouldThrow(ProductCannotBeRemoved::class)
            ->during('remove', [$product])
        ;
    }

    function it_uses_inner_persister_to_remove_a_product(
        ContextAwareDataPersisterInterface $persister,
        ProductInterface $product,
    ): void {
        $persister->remove($product, [])->shouldBeCalled();

        $this->remove($product, []);
    }
}
