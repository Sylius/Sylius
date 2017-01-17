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
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ProductOptionInterface extends
    CodeAwareInterface,
    ProductOptionTranslationInterface,
    TimestampableInterface,
    TranslatableInterface
{
    /**
     * @return int
     */
    public function getPosition();

    /**
     * @param int $position
     */
    public function setPosition($position);

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
