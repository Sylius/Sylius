<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Test\Services;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
interface EmailCheckerInterface
{
    /**
     * @param string $recipient
     *
     * @return bool
     */
    public function hasRecipient($recipient);

    /**
     * @param string $message
     *
     * @return bool
     */
    public function hasMessage($message);

    /**
     * @return int
     */
    public function getMessagesCount();

    /**
     * @return string
     */
    public function getSpoolDirectory();
}
