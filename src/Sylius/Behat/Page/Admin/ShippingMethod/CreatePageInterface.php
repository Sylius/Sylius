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
use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    /**
     * @param string $code
     */
    public function specifyCode(string $code): void;

    /**
     * @param int|null $position
     */
    public function specifyPosition(?int $position): void;

    /**
     * @param string $name
     * @param string $language
     */
    public function nameIt(string $name, string $language): void;

    /**
     * @param string $description
     * @param string $languageCode
     */
    public function describeIt(string $description, string $languageCode): void;

    /**
     * @param string $channelCode
     * @param string $amount
     */
    public function specifyAmountForChannel(string $channelCode, string $amount): void;

    /**
     * @param string $name
     */
    public function chooseZone(string $name): void;

    /**
     * @param string $name
     */
    public function chooseCalculator(string $name): void;

    /**
     * @return string $channelName
     */
    public function checkChannel($channelName): string;

    /**
     * @param string $channelCode
     *
     * @return string
     *
     * @throws ElementNotFoundException
     */
    public function getValidationMessageForAmount(string $channelCode): string;
}
