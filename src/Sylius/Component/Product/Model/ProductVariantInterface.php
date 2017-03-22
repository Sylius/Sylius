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
use Sylius\Component\Resource\Model\TranslatableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ProductVariantInterface extends
    TimestampableInterface,
    ResourceInterface,
    CodeAwareInterface,
    TranslatableInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getDescriptor();

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
     * @return int
     */
    public function getPosition();

    /**
     * @param int $position
     */
    public function setPosition($position);
}
