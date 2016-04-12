<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Service;

use Sylius\Behat\Exception\NotificationExpectationMismatchException;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Service\Accessor\NotificationAccessorInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
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
     * 
     * @throws NotificationExpectationMismatchException
     */
    public function checkCreationNotification($resource)
    {
        $message = sprintf('%s has been successfully created.', $this->humanizeResourceName($resource));

        $this->checkNotification($message, NotificationType::success());
    }

    /**
     * {@inheritdoc}
     * 
     * @throws NotificationExpectationMismatchException
     */
    public function checkDeletionNotification($resource)
    {
        $message = sprintf('%s has been successfully deleted.', $this->humanizeResourceName($resource));

        $this->checkNotification($message, NotificationType::success());
    }

    /**
     * {@inheritdoc}
     * 
     * @throws NotificationExpectationMismatchException
     */
    public function checkEditionNotification($resource)
    {
        $message = sprintf('%s has been successfully updated.', $this->humanizeResourceName($resource));

        $this->checkNotification($message, NotificationType::success());
    }

    /**
     * {@inheritdoc}
     */
    public function checkNotification($message, NotificationType $type)
    {
        if ($this->hasType($type) && $this->hasMessage($message)) {
            return;
        }

        throw new NotificationExpectationMismatchException(
            $type,
            $message,
            $this->notificationAccessor->getType(),
            $this->notificationAccessor->getMessage()
        );
    }

    /**
     * @param NotificationType $type
     *
     * @return bool
     */
    private function hasType(NotificationType $type)
    {
        return (string) $type === (string) $this->notificationAccessor->getType();
    }

    /**
     * @param string $message
     *
     * @return bool
     */
    private function hasMessage($message)
    {
        return false !== strpos($this->notificationAccessor->getMessage(), $message);
    }

    /**
     * @param string $resourceName
     *
     * @return string
     */
    private function humanizeResourceName($resourceName)
    {
        return ucfirst(str_replace('_', ' ', $resourceName));
    }
}
