<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Product\Model;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;

use Sylius\Component\Product\Model\AttributeValueInterface;
use Sylius\Component\Product\Model\OptionInterface;
use Sylius\Component\Product\Model\VariantInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ProductSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Product\Model\Product');
    }

    public function it_implements_Sylius_product_interface()
    {
        $this->shouldImplement('Sylius\Component\Product\Model\ProductInterface');
    }

    public function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_has_no_name_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    public function its_name_is_mutable()
    {
        $this->setName('Super product');
        $this->getName()->shouldReturn('Super product');
    }

    public function it_has_no_slug_by_default()
    {
        $this->getSlug()->shouldReturn(null);
    }

    public function its_slug_is_mutable()
    {
        $this->setSlug('super-product');
        $this->getSlug()->shouldReturn('super-product');
    }

    public function it_has_no_description_by_default()
    {
        $this->getDescription()->shouldReturn(null);
    }

    public function its_description_is_mutable()
    {
        $this->setDescription('This product is super cool because...');
        $this->getDescription()->shouldReturn('This product is super cool because...');
    }

    public function it_initializes_availability_date_by_default()
    {
        $this->getAvailableOn()->shouldHaveType('DateTime');
    }

    public function it_is_available_by_default()
    {
        $this->shouldBeAvailable();
    }

    public function its_availability_date_is_mutable()
    {
        $availableOn = new \DateTime('yesterday');

        $this->setAvailableOn($availableOn);
        $this->getAvailableOn()->shouldReturn($availableOn);
    }

    public function it_is_available_only_if_availability_date_is_in_past()
    {
        $availableOn = new \DateTime('yesterday');

        $this->setAvailableOn($availableOn);
        $this->shouldBeAvailable();

        $availableOn = new \DateTime('tomorrow');

        $this->setAvailableOn($availableOn);
        $this->shouldNotBeAvailable();
    }

    public function it_has_no_meta_keywords_by_default()
    {
        $this->getMetaKeywords()->shouldReturn(null);
    }

    public function its_meta_keywords_is_mutable()
    {
        $this->setMetaKeywords('foo, bar, baz');
        $this->getMetaKeywords()->shouldReturn('foo, bar, baz');
    }

    public function it_has_no_meta_description_by_default()
    {
        $this->getMetaDescription()->shouldReturn(null);
    }

    public function its_meta_description_is_mutable()
    {
        $this->setMetaDescription('Super product');
        $this->getMetaDescription()->shouldReturn('Super product');
    }

    public function it_initializes_attribute_collection_by_default()
    {
        $this->getAttributes()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    public function it_adds_attribute(AttributeValueInterface $attribute)
    {
        $attribute->setProduct($this)->shouldBeCalled();

        $this->addAttribute($attribute);
        $this->hasAttribute($attribute)->shouldReturn(true);
    }

    public function it_removes_attribute(AttributeValueInterface $attribute)
    {
        $attribute->setProduct($this)->shouldBeCalled();

        $this->addAttribute($attribute);
        $this->hasAttribute($attribute)->shouldReturn(true);

        $attribute->setProduct(null)->shouldBeCalled();

        $this->removeAttribute($attribute);
        $this->hasAttribute($attribute)->shouldReturn(false);
    }

    public function it_should_not_have_master_variant_by_default()
    {
        $this->getMasterVariant()->shouldReturn(null);
    }

    public function its_master_variant_should_be_mutable_and_define_given_variant_as_master(VariantInterface $variant)
    {
        $variant->setProduct($this)->shouldBeCalled();
        $variant->setMaster(true)->shouldBeCalled();

        $this->setMasterVariant($variant);
    }

    public function it_should_not_add_master_variant_twice_to_collection(VariantInterface $variant)
    {
        $variant->isMaster()->willReturn(true);

        $variant->setProduct($this)->shouldBeCalled();
        $variant->setMaster(true)->shouldBeCalled();

        $this->setMasterVariant($variant);
        $this->setMasterVariant($variant);

        $this->hasVariants()->shouldReturn(false);
    }

    public function its_hasVariants_should_return_false_if_no_variants_defined()
    {
        $this->hasVariants()->shouldReturn(false);
    }

    public function its_hasVariants_should_return_true_only_if_any_variants_defined(VariantInterface $variant)
    {
        $variant->isMaster()->willReturn(false);

        $variant->setProduct($this)->shouldBeCalled();

        $this->addVariant($variant);
        $this->hasVariants()->shouldReturn(true);
    }

    public function it_should_initialize_variants_collection_by_default()
    {
        $this->getVariants()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    public function it_should_initialize_option_collection_by_default()
    {
        $this->getOptions()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    public function its_hasOptions_should_return_false_if_no_options_defined()
    {
        $this->hasOptions()->shouldReturn(false);
    }

    public function its_hasOptions_should_return_true_only_if_any_options_defined(OptionInterface $option)
    {
        $this->addOption($option);
        $this->hasOptions()->shouldReturn(true);
    }

    public function its_options_collection_should_be_mutable(Collection $options)
    {
        $this->setOptions($options);
        $this->getOptions()->shouldReturn($options);
    }

    public function it_should_add_option_properly(OptionInterface $option)
    {
        $this->addOption($option);
        $this->hasOption($option)->shouldReturn(true);
    }

    public function it_should_remove_option_properly(OptionInterface $option)
    {
        $this->addOption($option);
        $this->hasOption($option)->shouldReturn(true);

        $this->removeOption($option);
        $this->hasOption($option)->shouldReturn(false);
    }

    public function it_initializes_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType('DateTime');
    }

    public function its_creation_date_is_mutable()
    {
        $date = new \DateTime('last year');

        $this->setCreatedAt($date);
        $this->getCreatedAt()->shouldReturn($date);
    }

    public function it_has_no_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    public function its_last_update_date_is_mutable()
    {
        $date = new \DateTime('last year');

        $this->setUpdatedAt($date);
        $this->getUpdatedAt()->shouldReturn($date);
    }

    public function it_has_no_deletion_date_by_default()
    {
        $this->getDeletedAt()->shouldReturn(null);
    }

    public function it_is_not_be_deleted_by_default()
    {
        $this->shouldNotBeDeleted();
    }

    public function its_deletion_date_is_mutable()
    {
        $deletedAt = new \DateTime();

        $this->setDeletedAt($deletedAt);
        $this->getDeletedAt()->shouldReturn($deletedAt);
    }

    public function it_is_deleted_only_if_deletion_date_is_in_past()
    {
        $deletedAt = new \DateTime('yesterday');

        $this->setDeletedAt($deletedAt);
        $this->shouldBeDeleted();

        $deletedAt = new \DateTime('tomorrow');

        $this->setDeletedAt($deletedAt);
        $this->shouldNotBeDeleted();
    }

    public function it_has_fluent_interface()
    {
        $date = new \DateTime();

        $this->setName('Foo')->shouldReturn($this);
        $this->setSlug('product-foo')->shouldReturn($this);
        $this->setDescription('Foo')->shouldReturn($this);
        $this->setAvailableOn($date)->shouldReturn($this);
        $this->setMetaDescription('SEO bla bla')->shouldReturn($this);
        $this->setMetaKeywords('foo, bar, baz')->shouldReturn($this);
        $this->setCreatedAt($date)->shouldReturn($this);
        $this->setUpdatedAt($date)->shouldReturn($this);
        $this->setDeletedAt($date)->shouldReturn($this);
    }
}
