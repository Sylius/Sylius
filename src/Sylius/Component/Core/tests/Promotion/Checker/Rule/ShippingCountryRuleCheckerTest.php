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

namespace Tests\Sylius\Component\Core\Promotion\Checker\Rule;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Promotion\Checker\Rule\ShippingCountryRuleChecker;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

final class ShippingCountryRuleCheckerTest extends TestCase
{
    private MockObject&RepositoryInterface $countryRepository;

    private MockObject&OrderInterface $subject;

    private AddressInterface&MockObject $address;

    private CountryInterface&MockObject $country;

    private ShippingCountryRuleChecker $ruleChecker;

    protected function setUp(): void
    {
        $this->countryRepository = $this->createMock(RepositoryInterface::class);
        $this->subject = $this->createMock(OrderInterface::class);
        $this->address = $this->createMock(AddressInterface::class);
        $this->country = $this->createMock(CountryInterface::class);
        $this->ruleChecker = new ShippingCountryRuleChecker($this->countryRepository);
    }

    public function testShouldImplementRuleCheckerInterface(): void
    {
        $this->assertInstanceOf(RuleCheckerInterface::class, $this->ruleChecker);
    }

    public function testShouldRecognizeNoShippingAddressAsNotEligible(): void
    {
        $this->subject->expects($this->once())->method('getShippingAddress')->willReturn(null);

        $this->assertFalse($this->ruleChecker->isEligible($this->subject, []));
    }

    public function testShouldRecognizeSubjectAsNotEligibleIfCountryDoesNotMatch(): void
    {
        $this->country->expects($this->once())->method('getCode')->willReturn('IE');
        $this->address->expects($this->once())->method('getCountryCode')->willReturn('IE');
        $this->subject->expects($this->once())->method('getShippingAddress')->willReturn($this->address);
        $this->countryRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => 'IE'])
            ->willReturn($this->country);

        $this->assertFalse($this->ruleChecker->isEligible($this->subject, ['country' => 'NL']));
    }

    public function testShouldRecognizeSubjectAsEligibleIfCountryMatch(): void
    {
        $this->country->expects($this->once())->method('getCode')->willReturn('IE');
        $this->address->expects($this->once())->method('getCountryCode')->willReturn('IE');
        $this->subject->expects($this->once())->method('getShippingAddress')->willReturn($this->address);
        $this->countryRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => 'IE'])
            ->willReturn($this->country);

        $this->assertTrue($this->ruleChecker->isEligible($this->subject, ['country' => 'IE']));
    }
}
