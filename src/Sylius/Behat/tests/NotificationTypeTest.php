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

namespace Tests\Sylius\Behat;

use PHPUnit\Framework\TestCase;
use Sylius\Behat\NotificationType;

final class NotificationTypeTest extends TestCase
{
    public function testInitializeWithSuccessValue(): void
    {
        $this->assertSame('success', NotificationType::success()->__toString());
    }

    public function testInitializeWithFailureValue(): void
    {
        $this->assertSame('failure', NotificationType::failure()->__toString());
    }

    public function testInitializeWithErrorValue(): void
    {
        $this->assertSame('error', NotificationType::error()->__toString());
    }

    public function testInitializeWithInfoValue(): void
    {
        $this->assertSame('info', NotificationType::info()->__toString());
    }
}
