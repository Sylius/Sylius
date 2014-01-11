<?php

/*
 * This file is part of the Sylius package.
 *
 * (c); Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\VariableProductBundle\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\ResourceBundle\Model\SoftDeletableInterface;
use Sylius\Bundle\ResourceBundle\Model\TimestampableInterface;

/**
 * Product variant interface.
 *
 * It's related only to products that implement CustomizableVariableProductInterface or FluidVariableProductInterface.
 * Allows setting values for different variations of product options.
 *
 * If some products don't need to have such features, they simply have only one master variant.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface VariantInterface extends SoftDeletableInterface, TimestampableInterface
{
    /**
     * Checks whether variant is master.
     *
     * @return Boolean
     */
    public function isMaster();

    /**
     * Defines whether variant is master.
     *
     * @param Boolean $master
     */
    public function setMaster($master);

    /**
     * Get presentation.
     *
     * This should be generated from option values
     * when no other is set.
     *
     * @return string
     */
    public function getPresentation();

    /**
     * Set custom presentation.
     *
     * @param string $presentation
     */
    public function setPresentation($presentation);

    /**
     * Get product.
     *
     * @return VariableProductInterface
     */
    public function getProduct();

    /**
     * Set product.
     *
     * @param VariableProductInterface|null $product
     */
    public function setProduct(VariableProductInterface $product = null);

    /**
     * Returns all option values.
     *
     * @return OptionValueInterface[]
     */
    public function getOptions();

    /**
     * Sets all variant options.
     *
     * @param Collection $options
     */
    public function setOptions(Collection $options);

    /**
     * Adds option value.
     *
     * @param OptionValueInterface $option
     */
    public function addOption(OptionValueInterface $option);

    /**
     * Removes option from variant.
     *
     * @param OptionValueInterface $option
     */
    public function removeOption(OptionValueInterface $option);

    /**
     * Checks whether variant has given option.
     *
     * @param OptionValueInterface $option
     *
     * @return Boolean
     */
    public function hasOption(OptionValueInterface $option);

    /**
     * Check whether the product is available.
     */
    public function isAvailable();

    /**
     * Return available on.
     *
     * @return \DateTime
     */
    public function getAvailableOn();

    /**
     * Set available on.
     *
     * @param \DateTime $availableOn
     */
    public function setAvailableOn(\DateTime $availableOn);

    /**
     * This method is used by product variants to inherit values
     * from a master variant, which is treated as a "template" for them.
     *
     * This is usable only when product has options.
     *
     * @param VariantInterface $masterVariant
     */
    public function setDefaults(VariantInterface $masterVariant);
}
