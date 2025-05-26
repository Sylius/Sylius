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

namespace Tests\Sylius\Component\Taxation\Resolver;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Taxation\Checker\TaxRateDateEligibilityCheckerInterface;
use Sylius\Component\Taxation\Model\TaxableInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Sylius\Component\Taxation\Model\TaxRateInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolver;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

final class TaxRateResolverTest extends TestCase
{
    /** @var RepositoryInterface<TaxRateInterface>&MockObject */
    private MockObject $taxRateRepository;

    /** @var TaxRateDateEligibilityCheckerInterface&MockObject */
    private MockObject $taxRateDateChecker;

    /** @var TaxableInterface&MockObject */
    private MockObject $taxable;

    private TaxRateResolver $taxRateResolver;

    protected function setUp(): void
    {
        $this->taxRateRepository = $this->createMock(RepositoryInterface::class);
        $this->taxRateDateChecker = $this->createMock(TaxRateDateEligibilityCheckerInterface::class);
        $this->taxable = $this->createMock(TaxableInterface::class);
        $this->taxRateResolver = new TaxRateResolver($this->taxRateRepository, $this->taxRateDateChecker);
    }

    public function testShouldImplementTaxRateResolverInterface(): void
    {
        $this->assertInstanceOf(TaxRateResolverInterface::class, $this->taxRateResolver);
    }

    public function testShouldReturnTaxRateForGivenTaxableCategory(): void
    {
        $taxCategory = $this->createMock(TaxCategoryInterface::class);
        $firstTaxRate = $this->createMock(TaxRateInterface::class);
        $secondTaxRate = $this->createMock(TaxRateInterface::class);
        $thirdTaxRate = $this->createMock(TaxRateInterface::class);
        $expectedCalls = [
            1 => [$firstTaxRate, false],
            2 => [$secondTaxRate, true],
        ];

        $this->taxable->expects($this->once())
            ->method('getTaxCategory')
            ->willReturn($taxCategory)
        ;
        $this->taxRateRepository->expects($this->once())
            ->method('findBy')
            ->with(['category' => $taxCategory])
            ->willReturn([$firstTaxRate, $secondTaxRate, $thirdTaxRate])
        ;
        $dateCheckerInvokedCount = $this->exactly(2);
        $this->taxRateDateChecker->expects($dateCheckerInvokedCount)
            ->method('isEligible')
            ->willReturnCallback(function (TaxRateInterface $taxRate) use ($dateCheckerInvokedCount, $expectedCalls): bool {
                [$expectedArgument, $returnValue] = $expectedCalls[$dateCheckerInvokedCount->numberOfInvocations()];
                $this->assertSame($expectedArgument, $taxRate);

                return $returnValue;
            });

        $this->assertSame($secondTaxRate, $this->taxRateResolver->resolve($this->taxable));
    }

    public function testShouldReturnNullIfTaxableDoesNotBelongToAnyCategory(): void
    {
        $this->taxable->expects($this->once())->method('getTaxCategory')->willReturn(null);

        $this->assertNull($this->taxRateResolver->resolve($this->taxable));
    }
}
