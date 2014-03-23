<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Variation\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Product\Model\ProductInterface;

/**
 * Should be implemented by models that support variants and options.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface VariableInterface
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
     * This method is not for checking if object has any variations.
     * It should determine if any variants (other than internal master) exist.
     *
     * @return Boolean
     */
    public function hasVariants();

    /**
     * Returns all object variants.
     * This collection should exclude the master variant.
     *
     * @return Collection|VariantInterface[]
     */
    public function getVariants();

    /**
     * Sets all object variants.
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
     * Removes variant from object.
     *
     * @param VariantInterface $variant
     */
    public function removeVariant(VariantInterface $variant);

    /**
     * Checks whether object has given variant.
     *
     * @param VariantInterface $variant
     *
     * @return Boolean
     */
    public function hasVariant(VariantInterface $variant);

    /**
     * This should return true only when object has options.
     *
     * @return Boolean
     */
    public function hasOptions();

    /**
     * Returns all object options.
     *
     * @return Collection|OptionInterface[]
     */
    public function getOptions();

    /**
     * Sets all object options.
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
     * Checks whether object has given option.
     *
     * @param OptionInterface $option
     *
     * @return Boolean
     */
    public function hasOption(OptionInterface $option);
}
