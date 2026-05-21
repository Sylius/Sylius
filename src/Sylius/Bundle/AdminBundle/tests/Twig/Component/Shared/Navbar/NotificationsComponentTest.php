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

namespace Tests\Sylius\Bundle\AdminBundle\Twig\Component\Shared\Navbar;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AdminBundle\Notification\NotificationProviderInterface;
use Sylius\Bundle\AdminBundle\Twig\Component\Shared\Navbar\NotificationsComponent;

final class NotificationsComponentTest extends TestCase
{
    private MockObject&NotificationProviderInterface $notificationProvider;

    private NotificationsComponent $notificationsComponent;

    private static string $hubUri = 'www.doesnotexist.test.com';

    public function setUp(): void
    {
        parent::setUp();

        $this->notificationProvider = $this->createMock(NotificationProviderInterface::class);

        $this->notificationsComponent = new NotificationsComponent($this->notificationProvider, true);
    }

    #[Test]
    public function it_gets_notifications_from_provider(): void
    {
        $this->notificationProvider->method('getNotifications')->willReturn(['version' => ['message' => 'sylius.ui.notifications.new_version_of_sylius_available']]);

        $notifications = $this->notificationsComponent->getNotifications();

        $this->assertNotEmpty($notifications);
        $this->assertSame($notifications['version'], [
            'message' => 'sylius.ui.notifications.new_version_of_sylius_available',
        ]);
    }

    #[Test]
    public function it_propagates_optional_notification_fields_unchanged(): void
    {
        $notification = [
            'message' => 'app.notification.legacy_secret_key',
            'message_parameters' => ['%gateway_name%' => 'Stripe EU'],
            'route' => 'sylius_admin_payment_method_update',
            'route_parameters' => ['id' => 42],
            'translation_domain' => 'messages',
            'type' => 'warning',
        ];

        $this->notificationProvider->method('getNotifications')->willReturn(['legacy_secret_key.42' => $notification]);

        $notifications = $this->notificationsComponent->getNotifications();

        $this->assertSame($notification, $notifications['legacy_secret_key.42']);
    }
}
