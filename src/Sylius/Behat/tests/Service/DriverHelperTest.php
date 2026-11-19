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

namespace Tests\Sylius\Behat\Service;

use Behat\Mink\Driver\DriverInterface;
use Behat\Mink\Session;
use DMore\ChromeDriver\ChromeDriver;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sylius\Behat\Service\DriverHelper;

#[CoversClass(DriverHelper::class)]
final class DriverHelperTest extends TestCase
{
    public function testItDoesNotWaitWhenDriverIsNotJavascript(): void
    {
        $driver = $this->createMock(DriverInterface::class);
        $session = $this->createMock(Session::class);
        $session->method('getDriver')->willReturn($driver);

        $session->expects($this->never())->method('wait');

        DriverHelper::waitForLiveComponentUpdate($session);
    }

    public function testItWaitsForTheLiveComponentRequestToStartAndFinishWhenDriverIsJavascript(): void
    {
        $driver = $this->createMock(ChromeDriver::class);
        $session = $this->createMock(Session::class);
        $session->method('getDriver')->willReturn($driver);

        $calls = [];
        $session->method('wait')->willReturnCallback(function (int $timeout, string $condition) use (&$calls): bool {
            $calls[] = ['timeout' => $timeout, 'condition' => $condition];

            return true;
        });

        DriverHelper::waitForLiveComponentUpdate($session, 5000);

        $this->assertCount(6, $calls, 'It should probe for a request to start and then to finish, three times.');

        foreach ([0, 2, 4] as $index) {
            $this->assertSame(300, $calls[$index]['timeout']);
            $this->assertStringContainsString('querySelectorAll(\'[data-controller~="live"][busy], [data-live-is-loading]\')', $calls[$index]['condition']);
            $this->assertStringContainsString('.length > 0', $calls[$index]['condition']);
        }

        foreach ([1, 3, 5] as $index) {
            // It waits until ALL Live Component requests have FINISHED.
            $this->assertSame(5000, $calls[$index]['timeout']);
            $this->assertStringContainsString('querySelectorAll(\'[data-controller~="live"][busy], [data-live-is-loading]\')', $calls[$index]['condition']);
            $this->assertStringContainsString('.length === 0', $calls[$index]['condition']);
            $this->assertStringContainsString("document.readyState === 'complete'", $calls[$index]['condition']);
        }
    }
}
