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
use Sylius\Bundle\ApiBundle\Exception\ProductVariantCannotBeRemoved;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ProductVariantDataPersisterSpec extends ObjectBehavior
{
    function let(ContextAwareDataPersisterInterface $dataPersister): void
    {
        $this->beConstructedWith($dataPersister);
    }

    function it_is_a_context_aware_persister(): void
    {
        $this->shouldImplement(ContextAwareDataPersisterInterface::class);
    }

    function it_supports_only_product_variant(ProductVariantInterface $productVariant): void
    {
        $this->supports(new \stdClass())->shouldReturn(false);
        $this->supports($productVariant)->shouldReturn(true);
    }

    function it_uses_inner_persister_to_persist_product_variant(
        ContextAwareDataPersisterInterface $dataPersister,
        ProductVariantInterface $productVariant,
    ): void {
        $dataPersister->persist($productVariant, [])->shouldBeCalled();

        $this->persist($productVariant);
    }

    function it_throws_cannot_be_removed_exception_if_constraint_fails_on_removal(
        ContextAwareDataPersisterInterface $dataPersister,
        ProductVariantInterface $productVariant,
    ): void {
        $dataPersister->remove($productVariant, [])->willThrow(ForeignKeyConstraintViolationException::class);

        $this->shouldThrow(ProductVariantCannotBeRemoved::class)->during('remove', [$productVariant]);
    }

    function it_uses_inner_persister_to_remove_product_variant(
        ContextAwareDataPersisterInterface $dataPersister,
        ProductVariantInterface $productVariant,
    ): void {
        $dataPersister->remove($productVariant, [])->shouldBeCalled();

        $this->remove($productVariant);
    }
}
