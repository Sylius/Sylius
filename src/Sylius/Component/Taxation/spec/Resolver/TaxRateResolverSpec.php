<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Taxation\Resolver;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxation\Checker\TaxRateDateEligibilityCheckerInterface;
use Sylius\Component\Taxation\Model\TaxableInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Sylius\Component\Taxation\Model\TaxRateInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;

final class TaxRateResolverSpec extends ObjectBehavior
{
    function let(RepositoryInterface $taxRateRepository, TaxRateDateEligibilityCheckerInterface $taxRateDateChecker): void
    {
        $this->beConstructedWith($taxRateRepository, $taxRateDateChecker);
    }

    function it_implements_tax_rate_resolver_interface(): void
    {
        $this->shouldImplement(TaxRateResolverInterface::class);
    }

    function it_returns_tax_rate_for_given_taxable_category(
        RepositoryInterface $taxRateRepository,
        TaxRateDateEligibilityCheckerInterface $taxRateDateChecker,
        TaxableInterface $taxable,
        TaxCategoryInterface $taxCategory,
        TaxRateInterface $firstTaxRate,
        TaxRateInterface $secondTaxRate,
        TaxRateInterface $thirdTaxRate,
    ): void {
        $taxable->getTaxCategory()->willReturn($taxCategory);
        $taxRateRepository
            ->findBy(['category' => $taxCategory])
            ->shouldBeCalled()
            ->willReturn([$firstTaxRate, $secondTaxRate, $thirdTaxRate])
        ;

        $taxRateDateChecker->isEligible($firstTaxRate)->willReturn(false);
        $taxRateDateChecker->isEligible($secondTaxRate)->willReturn(true);
        $taxRateDateChecker->isEligible($thirdTaxRate)->willReturn(true);

        $this->resolve($taxable)->shouldReturn($secondTaxRate);
    }

    function it_returns_null_if_taxable_does_not_belong_to_any_category(
        TaxableInterface $taxable,
    ): void {
        $taxable->getTaxCategory()->willReturn(null);

        $this->resolve($taxable)->shouldReturn(null);
    }
}
