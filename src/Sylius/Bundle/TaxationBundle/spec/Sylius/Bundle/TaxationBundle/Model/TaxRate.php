<?php

namespace spec\Sylius\Bundle\TaxationBundle\Model;

use PHPSpec2\ObjectBehavior;

/**
 * Tax rate model spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class TaxRate extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxationBundle\Model\TaxRate');
    }

    function it_should_be_a_Sylius_tax_rate()
    {
        $this->shouldImplement('Sylius\Bundle\TaxationBundle\Model\TaxRateInterface');
    }

    function it_should_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_should_not_belong_to_category_by_default()
    {
        $this->getCategory()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\TaxationBundle\Model\TaxCategoryInterface $category
     */
    function it_should_allow_assigning_itself_to_category($category)
    {
        $this->setCategory($category);
        $this->getCategory()->shouldReturn($category);
    }

    /**
     * @param Sylius\Bundle\TaxationBundle\Model\TaxCategoryInterface $category
     */
    function it_should_allow_detaching_itself_from_category($category)
    {
        $this->setCategory($category);
        $this->getCategory()->shouldReturn($category);

        $this->setCategory(null);
        $this->getCategory()->shouldReturn(null);
    }

    function it_should_be_unnamed_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_should_be_mutable()
    {
        $this->setName('Taxable goods');
        $this->getName()->shouldReturn('Taxable goods');
    }

    function it_should_have_amount_equal_to_0_by_default()
    {
        $this->getAmount()->shouldReturn(0);
    }

    function its_amount_should_be_mutable()
    {
        $this->setAmount(0.23);
        $this->getAmount()->shouldReturn(0.23);
    }

    function it_should_not_be_included_in_price_by_default()
    {
        $this->shouldNotBeIncludedInPrice();
    }

    function its_inclusion_in_price_should_be_mutable()
    {
        $this->setIncludedInPrice(true);
        $this->shouldBeIncludedInPrice();
    }

    function it_should_not_have_calculator_defined_by_default()
    {
        $this->getCalculator()->shouldReturn(null);
    }

    function its_calculator_should_be_mutable()
    {
        $this->setCalculator('default');
        $this->getCalculator()->shouldReturn('default');
    }

    function it_should_initialize_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType('DateTime');
    }

    function it_should_not_have_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }
}
