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

namespace Sylius\Behat\Page\Admin\ProductVariant;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    public function specifyPrice(string $price, string $channelName): void;

    public function specifyOriginalPrice(string $originalPrice, string $channelName): void;

    public function specifyHeightWidthDepthAndWeight(string $height, string $width, string $depth, string $weight): void;

    public function specifyCode(string $code): void;

    public function specifyCurrentStock(string $currentStock): void;

    public function nameItIn(string $name, string $language): void;

    public function selectOption(string $optionName, string $optionValue): void;

    public function choosePricingCalculator(string $name): void;

    public function getValidationMessageForForm(): string;

    public function selectShippingCategory(string $shippingCategoryName): void;

    public function getPricesValidationMessage(): string;

    public function setShippingRequired(bool $isShippingRequired): void;
}
