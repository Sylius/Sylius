<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Archetype\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Attribute\Model\AttributeInterface as BaseAttributeInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Variation\Model\OptionInterface as BaseOptionInterface;

/**
 * The archetype defines the template for new objects to be created from
 * which these object will inherit options and attributes from an instance
 * of this class.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Adam Elsodaney <adam.elso@gmail.com>
 */
interface ArchetypeInterface extends TimestampableInterface, ArchetypeTranslationInterface
{
    /**
     * Returns all prototype attributes.
     *
     * @return Collection|BaseAttributeInterface[]
     */
    public function getAttributes();

    /**
     * Sets all prototype attributes.
     *
     * @param Collection|BaseAttributeInterface[] $attributes
     */
    public function setAttributes(Collection $attributes);

    /**
     * Adds attribute.
     *
     * @param BaseAttributeInterface $attribute
     */
    public function addAttribute(BaseAttributeInterface $attribute);

    /**
     * Removes attribute from prototype.
     *
     * @param BaseAttributeInterface $attribute
     */
    public function removeAttribute(BaseAttributeInterface $attribute);

    /**
     * Checks whether prototype has given attribute.
     *
     * @param BaseAttributeInterface $attribute
     *
     * @return Boolean
     */
    public function hasAttribute(BaseAttributeInterface $attribute);

    /**
     * Returns all prototype options.
     *
     * @return Collection|BaseOptionInterface[]
     */
    public function getOptions();

    /**
     * Sets all prototype options.
     *
     * @param Collection|BaseOptionInterface[] $options
     */
    public function setOptions(Collection $options);

    /**
     * Adds option.
     *
     * @param BaseOptionInterface $option
     */
    public function addOption(BaseOptionInterface $option);

    /**
     * Removes option from prototype.
     *
     * @param BaseOptionInterface $option
     */
    public function removeOption(BaseOptionInterface $option);

    /**
     * Checks whether prototype has given option.
     *
     * @param BaseOptionInterface $option
     *
     * @return boolean
     */
    public function hasOption(BaseOptionInterface $option);

    /**
     * @return boolean
     */
    public function hasParent();

    /**
     * @param null|ArchetypeInterface $parent
     */
    public function setParent(ArchetypeInterface $parent = null);

    /**
     * @return null|ArchetypeInterface
     */
    public function getParent();
}
