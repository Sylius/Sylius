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
     * @param string $recipient
     *
     * @return bool
     */
    public function hasMessageTo($message, $recipient);

    /**
     * @param string $recipient
     *
     * @return int
     */
    public function countMessagesTo($recipient);

    /**
     * @return string
     */
    public function getSpoolDirectory();
}
