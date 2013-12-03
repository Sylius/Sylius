<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Model\Product;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ProductSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Model\Product');
    }

    function it_implements_Sylius_core_product_interface()
    {
        $this->shouldImplement('Sylius\Bundle\CoreBundle\Model\ProductInterface');
    }

    function it_extends_Sylius_variable_product()
    {
        $this->shouldHaveType('Sylius\Bundle\VariableProductBundle\Model\VariableProduct');
    }

    function it_does_not_have_short_description_by_default()
    {
        $this->getShortDescription()->shouldReturn(null);
    }

    function its_short_description_is_mutable()
    {
        $this->setShortDescription('Amazing product...');
        $this->getShortDescription()->shouldReturn('Amazing product...');
    }

    function it_initializes_taxon_collection_by_default()
    {
        $this->getTaxons()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    /**
     * @param Doctrine\Common\Collections\Collection $taxons
     */
    function its_taxons_are_mutable($taxons)
    {
        $this->setTaxons($taxons);
        $this->getTaxons()->shouldReturn($taxons);
    }

    function its_price_is_mutable()
    {
        $this->setPrice(4.99);
        $this->getPrice()->shouldReturn(4.99);
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
        $this->shouldImplement('Sylius\Bundle\TaxationBundle\Model\TaxableInterface');
    }

    function it_does_not_have_tax_category_by_default()
    {
        $this->getTaxCategory()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\TaxationBundle\Model\TaxCategoryInterface $taxCategory
     */
    function it_allows_setting_the_tax_category($taxCategory)
    {
        $this->setTaxCategory($taxCategory);
        $this->getTaxCategory()->shouldReturn($taxCategory);
    }

    /**
     * @param Sylius\Bundle\TaxationBundle\Model\TaxCategoryInterface $taxCategory
     */
    function it_allows_resetting_the_tax_category($taxCategory)
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

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingCategoryInterface $shippingCategory
     */
    function its_shipping_category_is_mutable($shippingCategory)
    {
        $this->setShippingCategory($shippingCategory);
        $this->getShippingCategory()->shouldReturn($shippingCategory);
    }

    function it_has_no_restricted_zone_by_default()
    {
        $this->getRestrictedZone()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneInterface $zone
     */
    function its_restricted_zone_is_mutable($zone)
    {
        $this->setRestrictedZone($zone);
        $this->getRestrictedZone()->shouldReturn($zone);
    }
}
