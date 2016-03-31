<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Exception;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class NotificationExpectationMismatchException extends \RuntimeException
{
    /**
     * @param string $missingNotificationType
     * @param string $missingNotification
     * @param string $foundedNotificationType
     * @param string $foundedNotification
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct(
        $missingNotificationType,
        $missingNotification,
        $foundedNotificationType,
        $foundedNotification,
        $code = 0, 
        \Exception $previous = null
    ) {
        $message = sprintf(
            "Expected *%s* notification with a \"%s\" message was not found.\n *%s* notification with a \"%s\" message has been found.",
            $missingNotificationType,
            $missingNotification,
            $foundedNotificationType,
            $foundedNotification
        );

        parent::__construct($message, $code, $previous);
    }
}
