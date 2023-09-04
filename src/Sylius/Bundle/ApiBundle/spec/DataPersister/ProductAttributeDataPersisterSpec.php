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
use Sylius\Component\Attribute\AttributeType\AttributeTypeInterface;
use Sylius\Component\Attribute\AttributeType\TextAttributeType;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;

final class ProductAttributeDataPersisterSpec extends ObjectBehavior
{
    function let(ContextAwareDataPersisterInterface $persister, ServiceRegistryInterface $typesRegistry): void
    {
        $this->beConstructedWith($persister, $typesRegistry);
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

    function it_uses_inner_persister_to_persist_product_attribute_when_storage_type_is_set(
        ContextAwareDataPersisterInterface $persister,
        ProductAttributeInterface $productAttribute,
    ): void {
        $productAttribute->getType()->willReturn(TextAttributeType::TYPE);
        $productAttribute->getStorageType()->willReturn(AttributeValueInterface::STORAGE_TEXT);

        $persister->persist($productAttribute, [])->shouldBeCalled();

        $this->persist($productAttribute, []);
    }

    function it_sets_storage_type_when_none_is_set_but_type_is(
        ContextAwareDataPersisterInterface $persister,
        ServiceRegistryInterface $typesRegistry,
        AttributeTypeInterface $attributeType,
        ProductAttributeInterface $productAttribute,
    ): void {
        $productAttribute->getType()->willReturn(TextAttributeType::TYPE);
        $productAttribute->getStorageType()->willReturn(null);

        $attributeType->getStorageType()->willReturn(AttributeValueInterface::STORAGE_TEXT);
        $typesRegistry->get(TextAttributeType::TYPE)->willReturn($attributeType);

        $productAttribute->setStorageType(AttributeValueInterface::STORAGE_TEXT)->shouldBeCalled();

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
