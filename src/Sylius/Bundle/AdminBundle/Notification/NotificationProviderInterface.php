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

namespace Sylius\Bundle\AdminBundle\Notification;

interface NotificationProviderInterface
{
    /**
     * @param array<mixed> $context
     *
     * @return array<array-key, mixed>
     */
    public function getNotifications(array $context = []): array;

    /** @param array<mixed> $context */
    public function supports(array $context = []): bool;
}
