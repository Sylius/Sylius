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

namespace Tests\Sylius\Component\Core\Distributor;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Distributor\ProportionalIntegerDistributor;
use Sylius\Component\Core\Distributor\ProportionalIntegerDistributorInterface;

final class ProportionalIntegerDistributorTest extends TestCase
{
    private ProportionalIntegerDistributor $distributor;

    protected function setUp(): void
    {
        $this->distributor = new ProportionalIntegerDistributor();
    }

    public function testShouldImplementProportionalIntegerDistributorInterface(): void
    {
        $this->assertInstanceOf(ProportionalIntegerDistributorInterface::class, $this->distributor);
    }

    public function testShouldDistributeIntegerBasedOnElementsParticipationInTotal(): void
    {
        $this->assertSame([150, 75, 75], $this->distributor->distribute([4000, 2000, 2000], 300));
    }

    public function testShouldDistributeNegativeIntegerBasedOnElementsParticipationInTotal(): void
    {
        $this->assertSame([-150, -75, -75], $this->distributor->distribute([4000, 2000, 2000], -300));
    }

    public function testShouldDistributeIntegerBasedOnElementsParticipationInTotalEvenIfItCanBeDividedEasily(): void
    {
        $this->assertSame([162, 52, 86], $this->distributor->distribute([4300, 1400, 2300], 300));
    }

    public function testShouldDistributeNegativeIntegerBasedOnElementsPariticpationInTotalEventIfItCanBeDividedEasily(): void
    {
        $this->assertSame([-162, -52, -86], $this->distributor->distribute([4300, 1400, 2300], -300));
    }

    public function testShouldDistributeIntegerEvenIfItsIndivisibleByNumberOfItems(): void
    {
        $this->assertSame([-161, -52, -86], $this->distributor->distribute([4300, 1400, 2300], -299));
    }

    public function testShouldDistributeAnIntegerEvenForNonDistributableItems(): void
    {
        $this->assertSame([0], $this->distributor->distribute([0], -299));
    }

    public function testShouldKeepOriginalKeysAfterComputation(): void
    {
        $this->assertSame(
            [
                1 => 150,
                3 => 75,
                6 => 75,
            ],
            $this->distributor->distribute([
                1 => 4000,
                3 => 2000,
                6 => 2000,
            ], 300),
        );
    }

    public function testShouldThrowExceptionIfAnyOfIntegerArrayElementIsNotInteger(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        /** @var int[] $integers */
        $integers = [4300, '1400', 2300];

        $this->distributor->distribute($integers, 300);
    }
}
