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

namespace Tests\Sylius\Bundle\CoreBundle\Calculator;

use DateTime;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Calculator\DelayStampCalculator;
use Sylius\Bundle\CoreBundle\Calculator\DelayStampCalculatorInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

final class DelayStampCalculatorTest extends TestCase
{
    private DelayStampCalculator $delayStampCalculator;

    protected function setUp(): void
    {
        $this->delayStampCalculator = new DelayStampCalculator();
    }

    public function testImplementsDelayStampCalculatorInterface(): void
    {
        $this->assertInstanceOf(DelayStampCalculatorInterface::class, $this->delayStampCalculator);
    }

    public function testCalculatesDelayStampFromGivenDates(): void
    {
        $currentTime = new DateTime('2021-11-11 20:20');
        $targetTime = new DateTime('2021-11-11 20:21');

        $expectedStamp = new DelayStamp(60000);

        $result = $this->delayStampCalculator->calculate($currentTime, $targetTime);

        $this->assertEquals($expectedStamp, $result);
    }

    public function testReturns0DelayIfDatesTargetTimeIsSmallerThanCurrentTime(): void
    {
        $currentTime = new DateTime('2021-11-11 20:21');
        $targetTime = new DateTime('2021-11-11 19:05');

        $expectedStamp = new DelayStamp(0);

        $result = $this->delayStampCalculator->calculate($currentTime, $targetTime);

        $this->assertEquals($expectedStamp, $result);
    }
}
