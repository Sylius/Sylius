<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Model;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Model\TaxonomyInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class ProductSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Model\Product');
    }

    function it_implements_Sylius_core_product_interface()
    {
        $this->shouldImplement('Sylius\Component\Core\Model\ProductInterface');
    }

    function it_extends_Sylius_product_model()
    {
        $this->shouldHaveType('Sylius\Component\Product\Model\Product');
    }

    function it_initializes_taxon_collection_by_default()
    {
        $this->getTaxons()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    function its_taxons_are_mutable(Collection $taxons)
    {
        $this->setTaxons($taxons);
        $this->getTaxons()->shouldReturn($taxons);
    }

    function it_can_get_taxons_from_specific_taxonomy(
        TaxonInterface $taxon1,
        TaxonInterface $taxon2,
        TaxonInterface $taxon3,
        TaxonomyInterface $taxonomy1,
        TaxonomyInterface $taxonomy2
    )
    {
        $taxon1->getTaxonomy()->willReturn($taxonomy1);
        $taxon2->getTaxonomy()->willReturn($taxonomy1);
        $taxon3->getTaxonomy()->willReturn($taxonomy2);

        $taxonomy1->getName()->willReturn('Category');
        $taxonomy2->getName()->willReturn('Brand');

        $this->addTaxon($taxon1);
        $this->addTaxon($taxon2);
        $this->addTaxon($taxon3);

        $this->getTaxons('category')->shouldHaveCount(2);
        $this->getTaxons('brand')->shouldHaveCount(1);
    }

    function its_price_is_mutable()
    {
        $this->setPrice(499);
        $this->getPrice()->shouldReturn(499);
    }

    function its_variant_selection_method_is_choice_by_default()
    {
        $this->getVariantSelectionMethod()->shouldReturn(Product::VARIANT_SELECTION_CHOICE);
    }

    function its_variant_selection_method_can_be_changed_to_option_match()
    {
        $this->setVariantSelectionMethod(Product::VARIANT_SELECTION_MATCH);
    }

    function it_throws_exception_if_any_other_value_is_given_as_variant_selection_method()
    {
        $this
            ->shouldThrow('InvalidArgumentException')
            ->duringSetVariantSelectionMethod('foo')
        ;
    }

    function it_implements_Sylius_taxable_interface()
    {
        $this->shouldImplement('Sylius\Component\Taxation\Model\TaxableInterface');
    }

    function it_does_not_have_tax_category_by_default()
    {
        $this->getTaxCategory()->shouldReturn(null);
    }

    function it_allows_setting_the_tax_category(TaxCategoryInterface $taxCategory)
    {
        $this->setTaxCategory($taxCategory);
        $this->getTaxCategory()->shouldReturn($taxCategory);
    }

    function it_allows_resetting_the_tax_category(TaxCategoryInterface $taxCategory)
    {
        $this->setTaxCategory($taxCategory);
        $this->getTaxCategory()->shouldReturn($taxCategory);

        $this->setTaxCategory(null);
        $this->getTaxCategory()->shouldReturn(null);
    }

    function it_has_no_shipping_category_by_default()
    {
        $this->getShippingCategory()->shouldReturn(null);
    }

    function its_shipping_category_is_mutable(ShippingCategoryInterface $shippingCategory)
    {
        $this->setShippingCategory($shippingCategory);
        $this->getShippingCategory()->shouldReturn($shippingCategory);
    }

    function it_has_no_restricted_zone_by_default()
    {
        $this->getRestrictedZone()->shouldReturn(null);
    }

    function its_restricted_zone_is_mutable(ZoneInterface $zone)
    {
        $this->setRestrictedZone($zone);
        $this->getRestrictedZone()->shouldReturn($zone);
    }
}
