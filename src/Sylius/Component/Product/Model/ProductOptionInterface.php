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
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;

/**
 * Model implementing this interface represents the option type, which can be
 * attached to an object.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ProductOptionInterface extends
    CodeAwareInterface,
    TimestampableInterface,
    ProductOptionTranslationInterface,
    TranslatableInterface
{
    /**
     * Returns all option values.
     *
     * @return Collection|ProductOptionValueInterface[]
     */
    public function getValues();

    /**
     * Sets all option values.
     *
     * @param Collection $optionValues
     */
    public function setValues(Collection $optionValues);

    /**
     * Adds option value.
     *
     * @param ProductOptionValueInterface $optionValue
     */
    public function addValue(ProductOptionValueInterface $optionValue);

    /**
     * Removes option value.
     *
     * @param ProductOptionValueInterface $optionValue
     */
    public function removeValue(ProductOptionValueInterface $optionValue);

    /**
     * Checks whether option has given value.
     *
     * @param ProductOptionValueInterface $optionValue
     *
     * @return bool
     */
    public function hasValue(ProductOptionValueInterface $optionValue);
}
