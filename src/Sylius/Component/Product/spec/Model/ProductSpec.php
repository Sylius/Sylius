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

namespace spec\Sylius\Component\Product\Model;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
final class ProductSpec extends ObjectBehavior
{
    function let()
    {
        $this->setCurrentLocale('en_US');
        $this->setFallbackLocale('en_US');
    }

    function it_implements_product_interface(): void
    {
        $this->shouldImplement(ProductInterface::class);
    }

    function it_implements_toggleable_interface(): void
    {
        $this->shouldImplement(ToggleableInterface::class);
    }

    function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_name_by_default(): void
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_is_mutable(): void
    {
        $this->setName('Super product');
        $this->getName()->shouldReturn('Super product');
    }

    function it_has_no_slug_by_default(): void
    {
        $this->getSlug()->shouldReturn(null);
    }

    function its_slug_is_mutable(): void
    {
        $this->setSlug('super-product');
        $this->getSlug()->shouldReturn('super-product');
    }

    function it_has_no_description_by_default(): void
    {
        $this->getDescription()->shouldReturn(null);
    }

    function its_description_is_mutable(): void
    {
        $this->setDescription('This product is super cool because...');
        $this->getDescription()->shouldReturn('This product is super cool because...');
    }

    function it_initializes_attribute_collection_by_default(): void
    {
        $this->getAttributes()->shouldHaveType(Collection::class);
    }

    function it_adds_attribute(ProductAttributeValueInterface $attribute): void
    {
        $attribute->setProduct($this)->shouldBeCalled();

        $this->addAttribute($attribute);
        $this->hasAttribute($attribute)->shouldReturn(true);
    }

    function it_removes_attribute(ProductAttributeValueInterface $attribute): void
    {
        $attribute->setProduct($this)->shouldBeCalled();

        $this->addAttribute($attribute);
        $this->hasAttribute($attribute)->shouldReturn(true);

        $attribute->setProduct(null)->shouldBeCalled();

        $this->removeAttribute($attribute);
        $this->hasAttribute($attribute)->shouldReturn(false);
    }

    function it_refuses_to_add_non_product_attribute(AttributeValueInterface $attribute): void
    {
        $this->shouldThrow('\InvalidArgumentException')->duringAddAttribute($attribute);
        $this->hasAttribute($attribute)->shouldReturn(false);
    }

    function it_refuses_to_remove_non_product_attribute(AttributeValueInterface $attribute): void
    {
        $this->shouldThrow('\InvalidArgumentException')->duringRemoveAttribute($attribute);
    }

    function it_has_no_variants_by_default(): void
    {
        $this->hasVariants()->shouldReturn(false);
    }

    function its_says_it_has_variants_only_if_multiple_variants_are_defined(
        ProductVariantInterface $firstVariant,
        ProductVariantInterface $secondVariant
    ): void {
        $firstVariant->setProduct($this)->shouldBeCalled();
        $secondVariant->setProduct($this)->shouldBeCalled();

        $this->addVariant($firstVariant);
        $this->addVariant($secondVariant);
        $this->hasVariants()->shouldReturn(true);
    }

    function it_initializes_variants_collection_by_default(): void
    {
        $this->getVariants()->shouldHaveType(Collection::class);
    }

    function it_does_not_include_unavailable_variants_in_available_variants(ProductVariantInterface $variant): void
    {
        $variant->setProduct($this)->shouldBeCalled();

        $this->addVariant($variant);
    }

    function it_returns_available_variants(
        ProductVariantInterface $unavailableVariant,
        ProductVariantInterface $variant
    ): void {
        $unavailableVariant->setProduct($this)->shouldBeCalled();
        $variant->setProduct($this)->shouldBeCalled();

        $this->addVariant($unavailableVariant);
        $this->addVariant($variant);
    }

    function it_initializes_options_collection_by_default(): void
    {
        $this->getOptions()->shouldHaveType(Collection::class);
    }

    function it_has_no_options_by_default(): void
    {
        $this->hasOptions()->shouldReturn(false);
    }

    function its_says_it_has_options_only_if_any_option_defined(ProductOptionInterface $option): void
    {
        $this->addOption($option);
        $this->hasOptions()->shouldReturn(true);
    }

    function it_adds_option_properly(ProductOptionInterface $option): void
    {
        $this->addOption($option);
        $this->hasOption($option)->shouldReturn(true);
    }

    function it_removes_option_properly(ProductOptionInterface $option): void
    {
        $this->addOption($option);
        $this->hasOption($option)->shouldReturn(true);

        $this->removeOption($option);
        $this->hasOption($option)->shouldReturn(false);
    }

    function it_initializes_creation_date_by_default(): void
    {
        $this->getCreatedAt()->shouldHaveType(\DateTimeInterface::class);
    }

    function its_creation_date_is_mutable(\DateTime $creationDate): void
    {
        $this->setCreatedAt($creationDate);
        $this->getCreatedAt()->shouldReturn($creationDate);
    }

    function it_has_no_last_update_date_by_default(): void
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    function its_last_update_date_is_mutable(\DateTime $updateDate): void
    {
        $this->setUpdatedAt($updateDate);
        $this->getUpdatedAt()->shouldReturn($updateDate);
    }

    function it_is_enabled_by_default(): void
    {
        $this->shouldBeEnabled();
    }

    function it_is_toggleable(): void
    {
        $this->disable();
        $this->shouldNotBeEnabled();

        $this->enable();
        $this->shouldBeEnabled();
    }

    function it_adds_association(ProductAssociationInterface $association): void
    {
        $association->setOwner($this)->shouldBeCalled();
        $this->addAssociation($association);

        $this->hasAssociation($association)->shouldReturn(true);
    }

    function it_allows_to_remove_association(ProductAssociationInterface $association): void
    {
        $association->setOwner($this)->shouldBeCalled();
        $association->setOwner(null)->shouldBeCalled();

        $this->addAssociation($association);
        $this->removeAssociation($association);

        $this->hasAssociation($association)->shouldReturn(false);
    }

    function it_is_simple_if_it_has_one_variant_and_no_options(ProductVariantInterface $variant): void
    {
        $variant->setProduct($this)->shouldBeCalled();
        $this->addVariant($variant);

        $this->isSimple()->shouldReturn(true);
        $this->isConfigurable()->shouldReturn(false);
    }

    function it_is_configurable_if_it_has_at_least_two_variants(
        ProductVariantInterface $firstVariant,
        ProductVariantInterface $secondVariant
    ): void {
        $firstVariant->setProduct($this)->shouldBeCalled();
        $this->addVariant($firstVariant);
        $secondVariant->setProduct($this)->shouldBeCalled();
        $this->addVariant($secondVariant);

        $this->isConfigurable()->shouldReturn(true);
        $this->isSimple()->shouldReturn(false);
    }

    function it_is_configurable_if_it_has_one_variant_and_at_least_one_option(
        ProductOptionInterface $option,
        ProductVariantInterface $variant
    ): void {
        $variant->setProduct($this)->shouldBeCalled();
        $this->addVariant($variant);
        $this->addOption($option);

        $this->isConfigurable()->shouldReturn(true);
        $this->isSimple()->shouldReturn(false);
    }
}
