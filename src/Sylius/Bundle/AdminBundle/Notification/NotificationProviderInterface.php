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
     * Each notification is identified by a string key. The value is a map with the following keys honored by the
     * default admin navbar notifications template:
     *
     * - `message` (string, required): translation key passed to the `trans` filter.
     * - `message_parameters` (array, optional): parameters passed to the `trans` filter.
     * - `uri` (string, optional): plain URI used as the notification link. Takes precedence over `route` when both are present.
     * - `route` (string, optional): when present (and `uri` is not), the notification renders as a link to this route.
     * - `route_parameters` (array, optional): parameters passed to `path()` together with `route`.
     * - `translation_domain` (string, optional): translation domain passed to the `trans` filter.
     * - `type` (string, optional): one of `info`, `warning`, `danger`. Defaults to `danger`.
     *
     * Providers MAY include additional keys for their own purposes; the default template ignores them.
     *
     * @param array<mixed> $context
     *
     * @return array<array-key, mixed>
     */
    public function getNotifications(array $context = []): array;

    /** @param array<mixed> $context */
    public function supports(array $context = []): bool;
}
