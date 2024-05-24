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
use Sylius\Bundle\ApiBundle\Exception\ProductAttributeCannotBeRemoved;
use Sylius\Component\Product\Model\ProductAttributeInterface;

final class ProductAttributeDataPersisterSpec extends ObjectBehavior
{
    function let(ContextAwareDataPersisterInterface $persister): void
    {
        $this->beConstructedWith($persister);
    }

    function it_is_a_context_aware_persister(): void
    {
        $this->shouldImplement(ContextAwareDataPersisterInterface::class);
    }

    function it_supports_only_product_attribute(ProductAttributeInterface $productAttribute): void
    {
        $this->supports(new \stdClass())->shouldReturn(false);
        $this->supports($productAttribute)->shouldReturn(true);
    }

    function it_uses_inner_persister_to_persist_product_attribute(
        ContextAwareDataPersisterInterface $persister,
        ProductAttributeInterface $productAttribute,
    ): void {
        $persister->persist($productAttribute, [])->shouldBeCalled();

        $this->persist($productAttribute, []);
    }

    function it_throws_cannot_be_removed_exception_if_constraint_fails_on_removal(
        ContextAwareDataPersisterInterface $persister,
        ProductAttributeInterface $productAttribute,
    ): void {
        $persister
            ->remove($productAttribute, [])
            ->willThrow(ForeignKeyConstraintViolationException::class)
        ;

        $this
            ->shouldThrow(ProductAttributeCannotBeRemoved::class)
            ->during('remove', [$productAttribute])
        ;
    }

    function it_uses_inner_persister_to_remove_product_attribute(
        ContextAwareDataPersisterInterface $persister,
        ProductAttributeInterface $productAttribute,
    ): void {
        $persister->remove($productAttribute, [])->shouldBeCalled();

        $this->remove($productAttribute, []);
    }
}
