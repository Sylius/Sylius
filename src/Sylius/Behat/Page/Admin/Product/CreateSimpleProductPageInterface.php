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

namespace Sylius\Behat\Page\Admin\Product;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;

interface CreateSimpleProductPageInterface extends BaseCreatePageInterface
{
    public function specifyPrice(string $channelName, int $price);

    public function specifyOriginalPrice(string $channelName, int $originalPrice);

    public function choosePricingCalculator(string $name);

    public function checkChannel(string $channelName);

    public function specifyCode(string $code);

    public function nameItIn(string $name, string $localeCode);

    public function specifySlugIn(string $slug, string $locale);

    public function addAttribute(string $attributeName, string $value, string $localeCode);

    public function getAttributeValidationErrors(string $attributeName, string $localeCode): string;

    public function removeAttribute(string $attributeName, string $localeCode);

    /**
     * @param string $type
     */
    public function attachImage(string $path, string $type = null);

    /**
     * @param string[] $productsNames
     */
    public function associateProducts(ProductAssociationTypeInterface $productAssociationType, array $productsNames);

    public function removeAssociatedProduct(string $productName, ProductAssociationTypeInterface $productAssociationType);

    public function activateLanguageTab(string $locale);

    public function selectShippingCategory(string $shippingCategoryName);

    public function setShippingRequired(bool $isShippingRequired);
}
