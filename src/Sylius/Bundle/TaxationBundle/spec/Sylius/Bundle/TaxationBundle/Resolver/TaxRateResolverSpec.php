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

use Doctrine\Common\Persistence\ObjectRepository;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\TaxationBundle\Model\TaxableInterface;
use Sylius\Bundle\TaxationBundle\Model\TaxCategoryInterface;
use Sylius\Bundle\TaxationBundle\Model\TaxRateInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class TaxRateResolverSpec extends ObjectBehavior
{
    function let(ObjectRepository $taxRateRepository)
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

    function it_returns_tax_rate_for_given_taxable_category(
        $taxRateRepository,
        TaxableInterface $taxable,
        TaxCategoryInterface $taxCategory,
        TaxRateInterface $taxRate
    )
    {
        $taxable->getTaxCategory()->willReturn($taxCategory);
        $taxRateRepository->findOneBy(array('category' => $taxCategory))->shouldBeCalled()->willReturn($taxRate);

        $this->resolve($taxable)->shouldReturn($taxRate);
    }

    function it_returns_null_if_tax_rate_for_given_taxable_category_does_not_exist(
        $taxRateRepository,
        TaxableInterface $taxable,
        TaxCategoryInterface $taxCategory
    )
    {
        $taxable->getTaxCategory()->willReturn($taxCategory);
        $taxRateRepository->findOneBy(array('category' => $taxCategory))->shouldBeCalled()->willReturn(null);

        $this->resolve($taxable)->shouldReturn(null);
    }

    function it_returns_null_if_taxable_does_not_belong_to_any_category(
        $taxRateRepository,
        TaxableInterface $taxable
    )
    {
        $taxable->getTaxCategory()->willReturn(null);

        $this->resolve($taxable)->shouldReturn(null);
    }
}
