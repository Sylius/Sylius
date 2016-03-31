<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Service\Accessor;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface NotificationAccessorInterface
{
    /**
     * @return bool
     */
    public function hasSuccessMessage();

    /**
     * @param string $message
     *
     * @return bool
     */
    public function hasMessage($message);

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @return string
     */
    public function getMessageType();
}
