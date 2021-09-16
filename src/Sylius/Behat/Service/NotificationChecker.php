<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Service;

use Sylius\Behat\Exception\NotificationExpectationMismatchException;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Service\Accessor\NotificationAccessorInterface;
use Webmozart\Assert\Assert;

final class NotificationChecker implements NotificationCheckerInterface
{
    private NotificationAccessorInterface $notificationAccessor;

    public function __construct(NotificationAccessorInterface $notificationAccessor)
    {
        $this->notificationAccessor = $notificationAccessor;
    }

    public function checkNotification(string $message, NotificationType $type): void
    {
        foreach ($this->notificationAccessor->getMessageElements() as $messageElement) {
            if (
                false !== strpos($messageElement->getText(), $message) &&
                $messageElement->hasClass($this->resolveClass($type))
            ) {
                return;
            }
        }

        throw new NotificationExpectationMismatchException($type, $message);
    }

    private function resolveClass(NotificationType $type): string
    {
        $typeClassMap = [
            'failure' => 'negative',
            'info' => 'info',
            'success' => 'positive',
        ];

        Assert::keyExists($typeClassMap, $type->__toString());

        return $typeClassMap[$type->__toString()];
    }
}
