<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Attribute\Model;

use Doctrine\Common\Collections\Collection;

interface AttributeSubjectInterface
{
    /**
     * @return Collection<array-key, AttributeValueInterface>
     */
    public function getAttributes(): Collection;

    /**
     * @return Collection<array-key, AttributeValueInterface>
     */
    public function getAttributesByLocale(
        string $localeCode,
        string $fallbackLocaleCode,
        ?string $baseLocaleCode = null,
    ): Collection;

    public function addAttribute(AttributeValueInterface $attribute): void;

    public function removeAttribute(AttributeValueInterface $attribute): void;

    public function hasAttribute(AttributeValueInterface $attribute): bool;

    public function hasAttributeByCodeAndLocale(string $attributeCode, ?string $localeCode = null): bool;

    public function getAttributeByCodeAndLocale(string $attributeCode, ?string $localeCode = null): ?AttributeValueInterface;
}
