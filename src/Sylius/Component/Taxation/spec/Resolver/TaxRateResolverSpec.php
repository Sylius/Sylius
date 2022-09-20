<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Taxation\Resolver;

use PhpSpec\ObjectBehavior;
use Sylius\Calendar\Provider\DateTimeProviderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxation\Checker\TaxRateDateCheckerInterface;
use Sylius\Component\Taxation\Model\TaxableInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Sylius\Component\Taxation\Model\TaxRateInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;

final class TaxRateResolverSpec extends ObjectBehavior
{
    function let(RepositoryInterface $taxRateRepository, TaxRateDateCheckerInterface $taxRateDateChecker): void
    {
        $this->beConstructedWith($taxRateRepository, $taxRateDateChecker);
    }

    function it_implements_tax_rate_resolver_interface(): void
    {
        $this->shouldImplement(TaxRateResolverInterface::class);
    }

    function it_returns_tax_rate_for_given_taxable_category(
        RepositoryInterface $taxRateRepository,
        TaxRateDateCheckerInterface $taxRateDateChecker,
        TaxableInterface $taxable,
        TaxCategoryInterface $taxCategory,
        TaxRateInterface $firstTaxRate,
        TaxRateInterface $secondTaxRate,
        TaxRateInterface $thirdTaxRate
    ): void {
        $taxable->getTaxCategory()->willReturn($taxCategory);
        $taxRateRepository
            ->findBy(['category' => $taxCategory])
            ->shouldBeCalled()
            ->willReturn([$firstTaxRate, $secondTaxRate, $thirdTaxRate])
        ;
//
//        $now = new \DateTime();
//        $calendar->now()->willReturn($now);
//
//        $firstTaxRate->isInDate($now)->willReturn(false);
//        $secondTaxRate->isInDate($now)->willReturn(true);
//        $thirdTaxRate->isInDate($now)->willReturn(true);
//
//        $this->resolve($taxable)->shouldReturn($secondTaxRate);

        $taxRateDateChecker->check([$firstTaxRate, $secondTaxRate, $thirdTaxRate])->willReturn($firstTaxRate);

        $this->resolve($taxable)->shouldReturn($firstTaxRate);
    }

//    function it_returns_null_if_tax_rate_for_given_taxable_category_does_not_exist(
//        RepositoryInterface $taxRateRepository,
//        DateTimeProviderInterface $calendar,
//        TaxableInterface $taxable,
//        TaxCategoryInterface $taxCategory,
//        TaxRateInterface $firstTaxRate,
//        TaxRateInterface $secondTaxRate,
//        TaxRateInterface $thirdTaxRate
//    ): void {
//        $taxable->getTaxCategory()->willReturn($taxCategory);
//        $taxRateRepository
//            ->findBy(['category' => $taxCategory])
//            ->shouldBeCalled()
//            ->willReturn([$firstTaxRate, $secondTaxRate, $thirdTaxRate])
//        ;
//
//        $now = new \DateTime();
//        $calendar->now()->willReturn($now);
//
//        $firstTaxRate->isInDate($now)->willReturn(false);
//        $secondTaxRate->isInDate($now)->willReturn(false);
//        $thirdTaxRate->isInDate($now)->willReturn(false);
//
//        $this->resolve($taxable)->shouldReturn(null);
//    }

    function it_returns_null_if_taxable_does_not_belong_to_any_category(
        TaxableInterface $taxable,
    ): void {
        $taxable->getTaxCategory()->willReturn(null);

        $this->resolve($taxable)->shouldReturn(null);
    }
}
