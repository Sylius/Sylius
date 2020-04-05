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
    public function specifyCode(string $code): void;

    public function specifyPosition(?int $position): void;

    public function nameIt(string $name, string $language): void;

    public function describeIt(string $description, string $languageCode): void;

    public function specifyAmountForChannel(string $channelCode, string $amount): void;

    public function chooseZone(string $name): void;

    public function chooseCalculator(string $name): void;

    public function checkChannel($channelName): void;

    /**
     * @throws ElementNotFoundException
     */
    public function getValidationMessageForAmount(string $channelCode): string;
}
