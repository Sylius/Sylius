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
use Sylius\Behat\Service\Accessor\NotificationAccessorInterface;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
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

        $this->checkSuccessNotificationMessage($message);
    }

    /**
     * {@inheritdoc}
     * 
     * @throws NotificationExpectationMismatchException
     */
    public function checkDeletionNotification($resource)
    {
        $message = sprintf('%s has been successfully deleted.', $this->humanizeResourceName($resource));

        $this->checkSuccessNotificationMessage($message);
    }

    /**
     * {@inheritdoc}
     * 
     * @throws NotificationExpectationMismatchException
     */
    public function checkEditionNotification($resource)
    {
        $message = sprintf('%s has been successfully updated.', $this->humanizeResourceName($resource));

        $this->checkSuccessNotificationMessage($message);
    }

    /**
     * @param string $message
     *
     * @throws NotificationExpectationMismatchException
     */
    public function checkSuccessNotificationMessage($message)
    {
        if ($this->notificationAccessor->hasSuccessMessage() && $this->notificationAccessor->hasMessage($message)) {
            return;
        }

        throw new NotificationExpectationMismatchException(
            'success',
            $message,
            $this->notificationAccessor->getMessageType(),
            $this->notificationAccessor->getMessage()
        );
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
