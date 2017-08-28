<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

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
    public function getAttributes(): Collection;

    /**
     * @param string $localeCode
     * @param string $fallbackLocaleCode
     *
     * @return Collection|AttributeValueInterface[]
     */
    public function getAttributesByLocale(string $localeCode, string $fallbackLocaleCode): Collection;

    /**
     * @param AttributeValueInterface $attribute
     */
    public function addAttribute(AttributeValueInterface $attribute): void;

    /**
     * @param AttributeValueInterface $attribute
     */
    public function removeAttribute(AttributeValueInterface $attribute): void;

    /**
     * @param AttributeValueInterface $attribute
     *
     * @return bool
     */
    public function hasAttribute(AttributeValueInterface $attribute): bool;

    /**
     * @param string $attributeCode
     * @param string|null $localeCode
     *
     * @return bool
     */
    public function hasAttributeByCodeAndLocale(string $attributeCode, ?string $localeCode = null): bool;

    /**
     * @param string $attributeCode
     * @param string|null $localeCode
     *
     * @return AttributeValueInterface|null
     */
    public function getAttributeByCodeAndLocale(string $attributeCode, ?string $localeCode = null): ?AttributeValueInterface;
}
