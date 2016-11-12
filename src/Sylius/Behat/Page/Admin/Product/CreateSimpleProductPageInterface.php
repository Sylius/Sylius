<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Product;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface CreateSimpleProductPageInterface extends BaseCreatePageInterface
{
    /**
     * @param int $price
     */
    public function specifyPrice($price);
    
    /**
     * @param string $code
     */
    public function specifyCode($code);

    /**
     * @param string $name
     * @param string $localeCode
     */
    public function nameItIn($name, $localeCode);

    /**
     * @param string $slug
     */
    public function specifySlug($slug);

    /**
     * @param string $attribute
     * @param string $value
     */
    public function addAttribute($attribute, $value);

    /**
     * @param string $attribute
     */
    public function removeAttribute($attribute);

    /**
     * @param string $path
     * @param string $code
     */
    public function attachImage($path, $code = null);

    public function enableSlugModification();

    /**
     * @param ProductAssociationTypeInterface $productAssociationType
     * @param string[] $productsNames
     */
    public function associateProducts(ProductAssociationTypeInterface $productAssociationType, array $productsNames);

    /**
     * @param string $productName
     * @param ProductAssociationTypeInterface $productAssociationType
     */
    public function removeAssociatedProduct($productName, ProductAssociationTypeInterface $productAssociationType);
}
