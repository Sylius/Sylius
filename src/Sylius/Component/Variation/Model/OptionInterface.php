<?php

/*
 * This file is part of the Sylius package.
 *
 * (c); Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Variation\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * Object option interface.
 *
 * Model implementing this interface represents the option type, which can be
 * attached to an object.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface OptionInterface extends TimestampableInterface
{
    /**
     * Get internal name.
     *
     * It is used only in backend so you can easily
     * separate similar options for different kind of objects.
     *
     * For example "T-Shirt size" and "Box size", both may have
     * presentation "Size", but their internal name should be different.
     *
     * @return string
     */
    public function getName();

    /**
     * Set internal name.
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * The name displayed to user.
     *
     * @return string
     */
    public function getPresentation();

    /**
     * Set presentation.
     *
     * @param string $presentation
     */
    public function setPresentation($presentation);

    /**
     * Returns all option values.
     *
     * @return Collection|OptionValueInterface[]
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
     * @param OptionValueInterface $optionValue
     */
    public function addValue(OptionValueInterface $optionValue);

    /**
     * Removes option value.
     *
     * @param OptionValueInterface $optionValue
     */
    public function removeValue(OptionValueInterface $optionValue);

    /**
     * Checks whether option has given value.
     *
     * @param OptionValueInterface $optionValue
     *
     * @return Boolean
     */
    public function hasValue(OptionValueInterface $optionValue);
}
