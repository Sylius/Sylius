<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Attribute\Model;

use Doctrine\Common\Collections\Collection;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface AttributeSubjectInterface
{
    /**
     * @return Collection|AttributeValueInterface[]
     */
    public function getAttributes();

    /**
     * @param string $localeCode
     * @param string $fallbackLocaleCode
     *
     * @return Collection|AttributeValueInterface[]
     */
    public function getAttributesByLocale($localeCode, $fallbackLocaleCode);

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
     * @param string $attributeCode
     * @param string|null $localeCode
     *
     * @return bool
     */
    public function hasAttributeByCodeAndLocale($attributeCode, $localeCode = null);

    /**
     * @param string $attributeCode
     * @param string|null $localeCode
     *
     * @return AttributeValueInterface
     */
    public function getAttributeByCodeAndLocale($attributeCode, $localeCode = null);
}
