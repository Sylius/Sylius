<?php

namespace spec\Sylius\Bundle\TaxationBundle\Model;

use PHPSpec2\ObjectBehavior;

/**
 * Tax category model spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class TaxCategory extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxationBundle\Model\TaxCategory');
    }

    function it_should_be_a_Sylius_tax_category()
    {
        $this->shouldImplement('Sylius\Bundle\TaxationBundle\Model\TaxCategoryInterface');
    }

    function it_should_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
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

    function it_should_not_have_description_by_default()
    {
        $this->getDescription()->shouldReturn(null);
    }

    function its_description_should_be_mutable()
    {
        $this->setDescription('All taxable goods');
        $this->getDescription()->shouldReturn('All taxable goods');
    }

    function it_should_initialize_rates_collection_by_default()
    {
        $this->getRates()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    /**
     * @param Sylius\Bundle\TaxationBundle\Model\TaxRateInterface $taxRate
     */
    function it_should_add_rates_properly($taxRate)
    {
        $this->hasRate($taxRate)->shouldReturn(false);

        $taxRate->setCategory($this)->shouldBeCalled();
        $this->addRate($taxRate);

        $this->hasRate($taxRate)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\TaxationBundle\Model\TaxRateInterface $taxRate
     */
    function it_should_remove_rates_properly($taxRate)
    {
        $this->hasRate($taxRate)->shouldReturn(false);

        $taxRate->setCategory($this)->shouldBeCalled();
        $this->addRate($taxRate);

        $taxRate->setCategory(null)->shouldBeCalled();
        $this->removeRate($taxRate);

        $this->hasRate($taxRate)->shouldReturn(false);
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
