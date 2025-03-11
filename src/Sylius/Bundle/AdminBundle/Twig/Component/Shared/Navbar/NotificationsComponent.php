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

namespace Sylius\Bundle\AdminBundle\Twig\Component\Shared\Navbar;

use Sylius\Bundle\AdminBundle\Notification\NotificationProviderInterface;
use Sylius\TwigHooks\Twig\Component\HookableComponentTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

class NotificationsComponent
{
    use HookableComponentTrait;

    public function __construct(
        protected readonly NotificationProviderInterface $notificationProvider,
        protected readonly bool $areNotificationsEnabled,
    ) {
    }

    /** @return array<array-key, mixed> */
    #[ExposeInTemplate(name: 'notifications')]
    public function getNotifications(): array
    {
        if (!$this->areNotificationsEnabled) {
            return [];
        }

        return $this->notificationProvider->getNotifications();
    }
}
