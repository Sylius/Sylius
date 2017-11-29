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

namespace Sylius\Behat\Page\Admin\ShippingMethod;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
    /**
     * @return bool
     */
    public function isCodeDisabled(): bool;

    /**
     * @param string $channelName
     *
     * @return bool
     */
    public function isAvailableInChannel(string $channelName): bool;

    public function enable(): void;

    public function disable(): void;

    public function removeZone(): void;

    /**
     * @param string $channelCode
     *
     * @return string
     *
     * @throws ElementNotFoundException
     */
    public function getValidationMessageForAmount(string $channelCode): string;
}
