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
    public function specifyCode(string $code);

    public function specifyPosition(?int $position);

    public function nameIt(string $name, string $language);

    public function describeIt(string $description, string $languageCode);

    public function specifyAmountForChannel(string $channelCode, string $amount);

    public function chooseZone(string $name);

    public function chooseCalculator(string $name);

    public function checkChannel(string $channelName): void;

    /**
     * @throws ElementNotFoundException
     */
    public function getValidationMessageForAmount(string $channelCode): string;
}
