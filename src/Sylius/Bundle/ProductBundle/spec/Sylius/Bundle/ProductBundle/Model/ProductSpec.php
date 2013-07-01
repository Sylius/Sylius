<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ProductBundle\Model;

use PhpSpec\ObjectBehavior;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ProductSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ProductBundle\Model\Product');
    }

    function it_implements_Sylius_product_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ProductBundle\Model\ProductInterface');
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_name_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_is_mutable()
    {
        $this->setName('Super product');
        $this->getName()->shouldReturn('Super product');
    }

    function it_has_no_slug_by_default()
    {
        $this->getSlug()->shouldReturn(null);
    }

    function its_slug_is_mutable()
    {
        $this->setSlug('super-product');
        $this->getSlug()->shouldReturn('super-product');
    }

    function it_has_no_description_by_default()
    {
        $this->getDescription()->shouldReturn(null);
    }

    function its_description_is_mutable()
    {
        $this->setDescription('This product is super cool because...');
        $this->getDescription()->shouldReturn('This product is super cool because...');
    }

    function it_initializes_availability_date_by_default()
    {
        $this->getAvailableOn()->shouldHaveType('DateTime');
    }

    function it_is_available_by_default()
    {
        $this->shouldBeAvailable();
    }

    function its_availability_date_is_mutable()
    {
        $availableOn = new \DateTime('yesterday');

        $this->setAvailableOn($availableOn);
        $this->getAvailableOn()->shouldReturn($availableOn);
    }

    function it_is_available_only_if_availability_date_is_in_past()
    {
        $availableOn = new \DateTime('yesterday');

        $this->setAvailableOn($availableOn);
        $this->shouldBeAvailable();

        $availableOn = new \DateTime('tomorrow');

        $this->setAvailableOn($availableOn);
        $this->shouldNotBeAvailable();
    }

    function it_has_no_meta_keywords_by_default()
    {
        $this->getMetaKeywords()->shouldReturn(null);
    }

    function its_meta_keywords_is_mutable()
    {
        $this->setMetaKeywords('foo, bar, baz');
        $this->getMetaKeywords()->shouldReturn('foo, bar, baz');
    }

    function it_has_no_meta_description_by_default()
    {
        $this->getMetaDescription()->shouldReturn(null);
    }

    function its_meta_description_is_mutable()
    {
        $this->setMetaDescription('Super product');
        $this->getMetaDescription()->shouldReturn('Super product');
    }

    function it_initializes_property_collection_by_default()
    {
        $this->getProperties()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    /**
     * @param Sylius\Bundle\ProductBundle\Model\ProductPropertyInterface $property
     */
    function it_adds_property($property)
    {
        $property->setProduct($this)->shouldBeCalled();

        $this->addProperty($property);
        $this->hasProperty($property)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\ProductBundle\Model\ProductPropertyInterface $property
     */
    function it_removes_property($property)
    {
        $property->setProduct($this)->shouldBeCalled();

        $this->addProperty($property);
        $this->hasProperty($property)->shouldReturn(true);

        $property->setProduct(null)->shouldBeCalled();

        $this->removeProperty($property);
        $this->hasProperty($property)->shouldReturn(false);
    }

    function it_initializes_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType('DateTime');
    }

    function its_creation_date_is_mutable()
    {
        $date = new \DateTime('last year');

        $this->setCreatedAt($date);
        $this->getCreatedAt()->shouldReturn($date);
    }

    function it_has_no_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    function its_last_update_date_is_mutable()
    {
        $date = new \DateTime('last year');

        $this->setUpdatedAt($date);
        $this->getUpdatedAt()->shouldReturn($date);
    }

    function it_has_no_deletion_date_by_default()
    {
        $this->getDeletedAt()->shouldReturn(null);
    }

    function it_is_not_be_deleted_by_default()
    {
        $this->shouldNotBeDeleted();
    }

    function its_deletion_date_is_mutable()
    {
        $deletedAt = new \DateTime();

        $this->setDeletedAt($deletedAt);
        $this->getDeletedAt()->shouldReturn($deletedAt);
    }

    function it_is_deleted_only_if_deletion_date_is_in_past()
    {
        $deletedAt = new \DateTime('yesterday');

        $this->setDeletedAt($deletedAt);
        $this->shouldBeDeleted();

        $deletedAt = new \DateTime('tomorrow');

        $this->setDeletedAt($deletedAt);
        $this->shouldNotBeDeleted();
    }

    /**
     * @param Sylius\Bundle\ProductBundle\Model\ProductPropertyInterface $property
     */
    function it_has_fluent_interface($property)
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
