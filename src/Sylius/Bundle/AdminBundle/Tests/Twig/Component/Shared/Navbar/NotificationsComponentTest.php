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

namespace Sylius\Bundle\AdminBundle\Tests\Twig\Component\Shared\Navbar;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Bundle\AdminBundle\Notification\NotificationProviderInterface;
use Sylius\Bundle\AdminBundle\Twig\Component\Shared\Navbar\NotificationsComponent;

final class NotificationsComponentTest extends TestCase
{
    use ProphecyTrait;

    private NotificationProviderInterface|ObjectProphecy $notificationProvider;

    private NotificationsComponent $notificationsComponent;

    private static string $hubUri = 'www.doesnotexist.test.com';

    public function setUp(): void
    {
        parent::setUp();

        $this->notificationProvider = $this->prophesize(NotificationProviderInterface::class);

        $this->notificationsComponent = new NotificationsComponent($this->notificationProvider->reveal(), true);
    }

    /** @test */
    public function it_gets_notifications_from_provider(): void
    {
        $this->notificationProvider->getNotifications()->willReturn(['version' => ['message' => 'sylius.ui.notifications.new_version_of_sylius_available']]);

        $notifications = $this->notificationsComponent->getNotifications();

        $this->assertNotEmpty($notifications);
        $this->assertSame($notifications['version'], [
            'message' => 'sylius.ui.notifications.new_version_of_sylius_available',
        ]);
    }
}
