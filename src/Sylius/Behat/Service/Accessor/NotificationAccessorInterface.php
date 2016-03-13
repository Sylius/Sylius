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
     * @param string $resourceName
     *
     * @return bool
     */
    public function isSuccessfullyCreatedFor($resourceName);

    /**
     * @param string $resourceName
     *
     * @return bool
     */
    public function isSuccessfullyUpdatedFor($resourceName);

    /**
     * @param string $resourceName
     *
     * @return bool
     */
    public function isSuccessfullyDeletedFor($resourceName);
}
