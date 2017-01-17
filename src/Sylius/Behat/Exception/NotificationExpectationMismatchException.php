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

use Sylius\Behat\NotificationType;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class NotificationExpectationMismatchException extends \RuntimeException
{
    public function __construct(
        NotificationType $expectedType,
        $expectedMessage,
        NotificationType $actualType,
        $actualMessage,
        $code = 0,
        \Exception $previous = null
    ) {
        $message = sprintf(
            "Expected *%s* notification with a \"%s\" message was not found.\n *%s* notification with a \"%s\" message has been found.",
            $expectedType,
            $expectedMessage,
            $actualType,
            $actualMessage
        );

        parent::__construct($message, $code, $previous);
    }
}
