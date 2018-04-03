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

final class NotificationChecker implements NotificationCheckerInterface
{
    /**
     * @var NotificationAccessorInterface
     */
    private $notificationAccessor;

    /**
     * @param NotificationAccessorInterface $notificationAccessor
     */
    public function __construct(NotificationAccessorInterface $notificationAccessor)
    {
        $this->notificationAccessor = $notificationAccessor;
    }

    /**
     * {@inheritdoc}
     */
    public function checkNotification(string $message, NotificationType $type): void
    {
        foreach ($this->notificationAccessor->getMessageElements() as $messageElement) {
            if (
                false !== strpos($messageElement->getText(), $message) &&
                $messageElement->hasClass($type->__toString() === 'success' ? 'positive' : 'negative')
            ) {
                return;
            }
        }

        throw new NotificationExpectationMismatchException($type, $message);
    }
}
