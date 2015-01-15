<?php

/*
 * This file is part of the Sylius package.
 *
 * (c); Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Attribute\Model;

use Doctrine\Common\Collections\Collection;

/**
 * Interface implemented by object which can be characterized
 * using the attributes.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface AttributeSubjectInterface
{
    /**
     * Returns all attributes of the subject.
     *
     * @return Collection|AttributeValueInterface[]
     */
    public function getAttributes();

    /**
     * Sets all attributes of the subject.
     *
     * @param Collection $attributes Array of AttributeValueInterface
     */
    public function setAttributes(Collection $attributes);

    /**
     * Adds an attribute to the subject.
     *
     * @param AttributeValueInterface $attribute
     */
    public function addAttribute(AttributeValueInterface $attribute);

    /**
     * Removes an attribute from the subject.
     *
     * @param AttributeValueInterface $attribute
     */
    public function removeAttribute(AttributeValueInterface $attribute);

    /**
     * Checks whether the subject has a given attribute.
     *
     * @param AttributeValueInterface $attribute
     *
     * @return Boolean
     */
    public function hasAttribute(AttributeValueInterface $attribute);

    /**
     * Checks whether the subject has a given attribute, access by name.
     *
     * @param string $attributeName
     *
     * @return Boolean
     */
    public function hasAttributeByName($attributeName);

    /**
     * Returns an attribute of the subject by its name.
     *
     * @param string $attributeName
     *
     * @return AttributeValueInterface
     */
    public function getAttributeByName($attributeName);
}
