<?php

/*
 * This file is part of the Sylius package.
 *
 * (c); Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Product\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * Used to generate full product form.
 * It simplifies product creation.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface PrototypeInterface extends TimestampableInterface
{
    /**
     * Get name, in most cases it will be displayed by user only in backend.
     * Can be something like 't-shirt' or 'tv'.
     *
     * @return string
     */
    public function getName();

    /**
     * Set name.
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Returns all prototype attributes.
     *
     * @return Collection|AttributeInterface[]
     */
    public function getAttributes();

    /**
     * Sets all prototype attributes.
     *
     * @param Collection $attributes
     */
    public function setAttributes(Collection $attributes);

    /**
     * Adds attribute.
     *
     * @param AttributeInterface $attribute
     */
    public function addAttribute(AttributeInterface $attribute);

    /**
     * Removes attribute from prototype.
     *
     * @param AttributeInterface $attribute
     */
    public function removeAttribute(AttributeInterface $attribute);

    /**
     * Checks whether prototype has given attribute.
     *
     * @param AttributeInterface $attribute
     *
     * @return Boolean
     */
    public function hasAttribute(AttributeInterface $attribute);

    /**
     * Returns all prototype options.
     *
     * @return Collection|OptionInterface[]
     */
    public function getOptions();

    /**
     * Sets all prototype options.
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
     * Removes option from prototype.
     *
     * @param OptionInterface $option
     */
    public function removeOption(OptionInterface $option);

    /**
     * Checks whether prototype has given option.
     *
     * @param OptionInterface $option
     *
     * @return Boolean
     */
    public function hasOption(OptionInterface $option);
}
