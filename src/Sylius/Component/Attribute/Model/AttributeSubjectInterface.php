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
     * @return Collection|AttributeValueInterface[]
     */
    public function getAttributes();

    /**
     * @param Collection $attributes
     */
    public function setAttributes(Collection $attributes);

    /**
     * @param AttributeValueInterface $attribute
     */
    public function addAttribute(AttributeValueInterface $attribute);

    /**
     * @param AttributeValueInterface $attribute
     */
    public function removeAttribute(AttributeValueInterface $attribute);

    /**
     * @param AttributeValueInterface $attribute
     *
     * @return bool
     */
    public function hasAttribute(AttributeValueInterface $attribute);

    /**
     * @param string $attributeName
     *
     * @return bool
     */
    public function hasAttributeByName($attributeName);

    /**
     * @param string $attributeName
     *
     * @return AttributeValueInterface
     */
    public function getAttributeByName($attributeName);
}
