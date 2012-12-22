<?php

namespace spec\Sylius\Bundle\TaxationBundle\Resolver;

use PHPSpec2\ObjectBehavior;

/**
 * Default tax rate resolver spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class TaxRateResolver extends ObjectBehavior
{
    /**
     * @param Doctrine\Common\Persistence\ObjectRepository $taxRateRepository
     */
    function let($taxRateRepository)
    {
        $this->beConstructedWith($taxRateRepository);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxationBundle\Resolver\TaxRateResolver');
    }

    function it_should_be_a_Sylius_tax_rate_resolver()
    {
        $this->shouldImplement('Sylius\Bundle\TaxationBundle\Resolver\TaxRateResolverInterface');
    }

    /**
     * @param Sylius\Bundle\TaxationBundle\Model\TaxableInterface     $taxable
     * @param Sylius\Bundle\TaxationBundle\Model\TaxCategoryInterface $taxCategory
     * @param Sylius\Bundle\TaxationBundle\Model\TaxRateInterface     $taxRate
     */
    function it_should_return_tax_rate_for_given_taxable_category($taxRateRepository, $taxable, $taxCategory, $taxRate)
    {
        $taxable->getTaxCategory()->willReturn($taxCategory);
        $taxRateRepository->findOneBy(array('category' => $taxCategory))->shouldBeCalled()->willReturn($taxRate);

        $this->resolve($taxable)->shouldReturn($taxRate);
    }

    /**
     * @param Sylius\Bundle\TaxationBundle\Model\TaxableInterface     $taxable
     * @param Sylius\Bundle\TaxationBundle\Model\TaxCategoryInterface $taxCategory
     */
    function it_should_return_null_if_tax_rate_for_given_taxable_category_does_not_exist($taxRateRepository, $taxable, $taxCategory)
    {
        $taxable->getTaxCategory()->willReturn($taxCategory);
        $taxRateRepository->findOneBy(array('category' => $taxCategory))->shouldBeCalled()->willReturn(null);

        $this->resolve($taxable)->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\TaxationBundle\Model\TaxableInterface $taxable
     */
    function it_should_return_null_if_taxable_does_not_belong_to_any_category($taxRateRepository, $taxable)
    {
        $taxable->getTaxCategory()->willReturn(null);

        $this->resolve($taxable)->shouldReturn(null);
    }
}
