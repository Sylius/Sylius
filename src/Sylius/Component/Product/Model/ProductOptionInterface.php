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
     * @return Collection|ProductOptionValueInterface[]
     */
    public function getValues();

    /**
     * @param ProductOptionValueInterface $optionValue
     */
    public function addValue(ProductOptionValueInterface $optionValue);

    /**
     * @param ProductOptionValueInterface $optionValue
     */
    public function removeValue(ProductOptionValueInterface $optionValue);

    /**
     * @param ProductOptionValueInterface $optionValue
     *
     * @return bool
     */
    public function hasValue(ProductOptionValueInterface $optionValue);
}
