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
    public function isRegistered();

    /**
     * @throws ElementNotFoundException If there is no delete account button on the page
     */
    public function deleteAccount();

    /**
     * @return string
     */
    public function getCustomerEmail();

    /**
     * @return string
     */
    public function getCustomerPhoneNumber();

    /**
     * @return string
     */
    public function getCustomerName();

    /**
     * @return \DateTimeInterface
     */
    public function getRegistrationDate();

    /**
     * @return string
     */
    public function getDefaultAddress();

    /**
     * @return bool
     */
    public function hasAccount();

    /**
     * @return bool
     */
    public function isSubscribedToNewsletter();

    /**
     * @param string $provinceName
     *
     * @return bool
     */
    public function hasDefaultAddressProvinceName($provinceName);

    /**
     * @return bool
     */
    public function hasVerifiedEmail();

    /**
     * @return string
     */
    public function getGroupName();

    /**
     * @return bool
     */
    public function hasEmailVerificationInformation();

    /**
     * @return bool
     */
    public function hasImpersonateButton();

    public function impersonate();

    /**
     * @return bool
     */
    public function hasCustomerPlacedAnyOrders();

    /**
     * @param string $channelName
     *
     * @return int
     */
    public function getOrdersCountInChannel($channelName);

    /**
     * @param string $channelName
     *
     * @return string
     */
    public function getOrdersTotalInChannel($channelName);

    /**
     * @param string $channelName
     *
     * @return string
     */
    public function getAverageTotalInChannel($channelName);

    /**
     * @return string
     */
    public function getSuccessFlashMessage();
}
