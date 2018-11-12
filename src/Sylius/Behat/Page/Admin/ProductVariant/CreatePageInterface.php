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
    public function specifyPrice(int $price, string $channelName);

    public function specifyOriginalPrice(int $originalPrice, string $channelName);

    public function specifyHeightWidthDepthAndWeight(int $height, int $width, int $depth, int $weight);

    public function specifyCode(string $code);

    public function specifyCurrentStock(int $currentStock);

    public function nameItIn(string $name, string $language);

    public function selectOption(string $optionName, string $optionValue);

    public function choosePricingCalculator(string $name);

    public function getValidationMessageForForm(): string;

    public function selectShippingCategory(string $shippingCategoryName);

    public function getPricesValidationMessage(): string;

    public function setShippingRequired(bool $isShippingRequired);
}
