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
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Variation\Model\OptionInterface;

/**
 * The archetype defines the template for new objects to be created from
 * which these object will inherit options and attributes from an instance
 * of this class.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Adam Elsodaney <adam.elso@gmail.com>
 */
interface ArchetypeInterface extends
    CodeAwareInterface,
    TimestampableInterface,
    ArchetypeTranslationInterface,
    TranslatableInterface
{
    /**
     * Returns all prototype attributes.
     *
     * @return Collection|AttributeInterface[]
     */
    public function getAttributes();

    /**
     * Sets all prototype attributes.
     *
     * @param Collection|AttributeInterface[] $attributes
     */
    public function setAttributes(Collection $attributes);

    /**
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
     * @return bool
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
     * @param Collection|OptionInterface[] $options
     */
    public function setOptions(Collection $options);

    /**
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
     * @return bool
     */
    public function hasOption(OptionInterface $option);

    /**
     * @return bool
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
