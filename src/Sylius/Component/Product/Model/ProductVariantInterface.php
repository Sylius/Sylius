<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Product\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ProductVariantInterface extends TimestampableInterface, ResourceInterface, CodeAwareInterface
{
    /**
     * This should be generated from option values
     * when no other is set.
     *
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return Collection|ProductOptionValueInterface[]
     */
    public function getOptionValues();

    /**
     * @param ProductOptionValueInterface $optionValue
     */
    public function addOptionValue(ProductOptionValueInterface $optionValue);

    /**
     * @param ProductOptionValueInterface $optionValue
     */
    public function removeOptionValue(ProductOptionValueInterface $optionValue);

    /**
     * @param ProductOptionValueInterface $optionValue
     *
     * @return bool
     */
    public function hasOptionValue(ProductOptionValueInterface $optionValue);

    /**
     * @return ProductInterface
     */
    public function getProduct();

    /**
     * @param null|ProductInterface $product
     */
    public function setProduct(ProductInterface $product = null);

    /**
     * @return bool
     */
    public function isAvailable();

    /**
     * @return \DateTime
     */
    public function getAvailableOn();

    /**
     * @param null|\DateTime $availableOn
     */
    public function setAvailableOn(\DateTime $availableOn = null);

    /**
     * @return \DateTime
     */
    public function getAvailableUntil();

    /**
     * @param null|\DateTime $availableUntil
     */
    public function setAvailableUntil(\DateTime $availableUntil = null);
}
