<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Taxation\Resolver;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxation\Model\TaxableInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Sylius\Component\Taxation\Model\TaxRateInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class TaxRateResolverSpec extends ObjectBehavior
{
    function let(RepositoryInterface $taxRateRepository)
    {
        $this->beConstructedWith($taxRateRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Taxation\Resolver\TaxRateResolver');
    }

    function it_implements_Sylius_tax_rate_resolver_interface()
    {
        $this->shouldImplement(TaxRateResolverInterface::class);
    }

    function it_returns_tax_rate_for_given_taxable_category(
        $taxRateRepository,
        TaxableInterface $taxable,
        TaxCategoryInterface $taxCategory,
        TaxRateInterface $taxRate
    ) {
        $taxable->getTaxCategory()->willReturn($taxCategory);
        $taxRateRepository->findOneBy(['category' => $taxCategory])->shouldBeCalled()->willReturn($taxRate);

        $this->resolve($taxable)->shouldReturn($taxRate);
    }

    function it_returns_null_if_tax_rate_for_given_taxable_category_does_not_exist(
        $taxRateRepository,
        TaxableInterface $taxable,
        TaxCategoryInterface $taxCategory
    ) {
        $taxable->getTaxCategory()->willReturn($taxCategory);
        $taxRateRepository->findOneBy(['category' => $taxCategory])->shouldBeCalled()->willReturn(null);

        $this->resolve($taxable)->shouldReturn(null);
    }

    function it_returns_null_if_taxable_does_not_belong_to_any_category(
        TaxableInterface $taxable
    ) {
        $taxable->getTaxCategory()->willReturn(null);

        $this->resolve($taxable)->shouldReturn(null);
    }
}
