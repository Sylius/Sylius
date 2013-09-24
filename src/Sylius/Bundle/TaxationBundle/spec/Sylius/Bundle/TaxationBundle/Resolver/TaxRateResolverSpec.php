<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\TaxationBundle\Resolver;

use PhpSpec\ObjectBehavior;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class TaxRateResolverSpec extends ObjectBehavior
{
    /**
     * @param Doctrine\Common\Persistence\ObjectRepository $taxRateRepository
     */
    function let($taxRateRepository)
    {
        $this->beConstructedWith($taxRateRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxationBundle\Resolver\TaxRateResolver');
    }

    function it_implements_Sylius_tax_rate_resolver_interface()
    {
        $this->shouldImplement('Sylius\Bundle\TaxationBundle\Resolver\TaxRateResolverInterface');
    }

    /**
     * @param Sylius\Bundle\TaxationBundle\Model\TaxableInterface     $taxable
     * @param Sylius\Bundle\TaxationBundle\Model\TaxCategoryInterface $taxCategory
     * @param Sylius\Bundle\TaxationBundle\Model\TaxRateInterface     $taxRate
     */
    function it_returns_tax_rate_for_given_taxable_category($taxRateRepository, $taxable, $taxCategory, $taxRate)
    {
        $taxable->getTaxCategory()->willReturn($taxCategory);
        $taxRateRepository->findOneBy(array('category' => $taxCategory))->shouldBeCalled()->willReturn($taxRate);

        $this->resolve($taxable)->shouldReturn($taxRate);
    }

    /**
     * @param Sylius\Bundle\TaxationBundle\Model\TaxableInterface     $taxable
     * @param Sylius\Bundle\TaxationBundle\Model\TaxCategoryInterface $taxCategory
     */
    function it_returns_null_if_tax_rate_for_given_taxable_category_does_not_exist($taxRateRepository, $taxable, $taxCategory)
    {
        $taxable->getTaxCategory()->willReturn($taxCategory);
        $taxRateRepository->findOneBy(array('category' => $taxCategory))->shouldBeCalled()->willReturn(null);

        $this->resolve($taxable)->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\TaxationBundle\Model\TaxableInterface $taxable
     */
    function it_returns_null_if_taxable_does_not_belong_to_any_category($taxRateRepository, $taxable)
    {
        $taxable->getTaxCategory()->willReturn(null);

        $this->resolve($taxable)->shouldReturn(null);
    }
}
