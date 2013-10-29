<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\VariableProductBundle\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\ProductBundle\Model\ProductInterface;

/**
 * Variable product interface.
 *
 * Should be implemented by models that support variants and options.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface VariableProductInterface extends ProductInterface
{
    /**
     * Returns master variant.
     *
     * @return VariantInterface
     */
    public function getMasterVariant();

    /**
     * Sets master variant.
     *
     * @param VariantInterface $variant
     */
    public function setMasterVariant(VariantInterface $variant);

    /**
     * Has any variants?
     * This method is not for checking if product is simple or customizable.
     * It should determine if any variants (other than internal master) exist.
     *
     * @return Boolean
     */
    public function hasVariants();

    /**
     * Returns all product variants.
     * This collection should exclude the master variant.
     *
     * @return VariantInterface[]
     */
    public function getVariants();

    /**
     * Return product variants which are available.
     * This collection should exclude the master variant.
     *
     * @return array An array or collection of VariantInterface
     */
    public function getAvailableVariants();

    /**
     * Sets all product variants.
     *
     * @param Collection $variants
     */
    public function setVariants(Collection $variants);

    /**
     * Adds variant.
     *
     * @param VariantInterface $variant
     */
    public function addVariant(VariantInterface $variant);

    /**
     * Removes variant from product.
     *
     * @param VariantInterface $variant
     */
    public function removeVariant(VariantInterface $variant);

    /**
     * Checks whether product has given variant.
     *
     * @param VariantInterface $variant
     *
     * @return Boolean
     */
    public function hasVariant(VariantInterface $variant);

    /**
     * Is customizable?
     * This should return true only when product has options.
     *
     * @return Boolean
     */
    public function hasOptions();

    /**
     * Returns all product options.
     *
     * @return OptionInterface[]
     */
    public function getOptions();

    /**
     * Sets all product options.
     *
     * @param Collection $options
     */
    public function setOptions(Collection $options);

    /**
     * Adds option.
     *
     * @param OptionInterface $option
     */
    public function addOption(OptionInterface $option);

    /**
     * Removes option from product.
     *
     * @param OptionInterface $option
     */
    public function removeOption(OptionInterface $option);

    /**
     * Checks whether product has given option.
     *
     * @param OptionInterface $option
     *
     * @return Boolean
     */
    public function hasOption(OptionInterface $option);
}
