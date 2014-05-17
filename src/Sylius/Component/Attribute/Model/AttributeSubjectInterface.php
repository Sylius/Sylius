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
     * Returns all product product properties.
     *
     * @return Collection|AttributeValueInterface[]
     */
    public function getAttributes();

    /**
     * Sets all product product properties.
     *
     * @param Collection $properties Array of AttributeValueInterface
     */
    public function setAttributes(Collection $properties);

    /**
     * Adds product attribute.
     *
     * @param AttributeValueInterface $attribute
     */
    public function addAttribute(AttributeValueInterface $attribute);

    /**
     * Removes product attribute from product.
     *
     * @param AttributeValueInterface $attribute
     */
    public function removeAttribute(AttributeValueInterface $attribute);

    /**
     * Checks whether product has given product attribute.
     *
     * @param AttributeValueInterface $attribute
     *
     * @return Boolean
     */
    public function hasAttribute(AttributeValueInterface $attribute);

    /**
     * Checks whether product has given product attribute, access by name.
     *
     * @param string $attributeName
     *
     * @return Boolean
     */
    public function hasAttributeByName($attributeName);

    /**
     * Returns a attribute by its name.
     *
     * @param string $attributeName
     *
     * @return AttributeValueInterface
     */
    public function getAttributeByName($attributeName);
}
