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

namespace Sylius\Behat\Page\Admin\Customer;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\PageInterface;

interface ShowPageInterface extends PageInterface
{
    /**
     * @return bool
     */
    public function isRegistered(): bool;

    /**
     * @throws ElementNotFoundException If there is no delete account button on the page
     */
    public function deleteAccount(): void;

    /**
     * @return string
     */
    public function getCustomerEmail(): string;

    /**
     * @return string
     */
    public function getCustomerPhoneNumber(): string;

    /**
     * @return string
     */
    public function getCustomerName(): string;

    /**
     * @return \DateTimeInterface
     */
    public function getRegistrationDate(): \DateTimeInterface;

    /**
     * @return string
     */
    public function getDefaultAddress(): string;

    /**
     * @return bool
     */
    public function hasAccount(): bool;

    /**
     * @return bool
     */
    public function isSubscribedToNewsletter(): bool;

    /**
     * @param string $provinceName
     *
     * @return bool
     */
    public function hasDefaultAddressProvinceName(string $provinceName): bool;

    /**
     * @return bool
     */
    public function hasVerifiedEmail(): bool;

    /**
     * @return string
     */
    public function getGroupName(): string;

    /**
     * @return bool
     */
    public function hasEmailVerificationInformation(): bool;

    /**
     * @return bool
     */
    public function hasImpersonateButton(): bool;

    public function impersonate(): void;

    /**
     * @return bool
     */
    public function hasCustomerPlacedAnyOrders(): bool;

    /**
     * @param string $channelName
     *
     * @return int
     */
    public function getOrdersCountInChannel(string $channelName): int;

    /**
     * @param string $channelName
     *
     * @return string
     */
    public function getOrdersTotalInChannel(string $channelName): string;

    /**
     * @param string $channelName
     *
     * @return string
     */
    public function getAverageTotalInChannel(string $channelName): string;

    /**
     * @return string
     */
    public function getSuccessFlashMessage(): string;
}
