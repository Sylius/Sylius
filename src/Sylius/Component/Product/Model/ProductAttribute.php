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

namespace Sylius\Component\Product\Model;

use Sylius\Component\Attribute\Model\Attribute as BaseAttribute;
use Sylius\Component\Attribute\Model\AttributeTranslationInterface;

class ProductAttribute extends BaseAttribute implements ProductAttributeInterface
{
    public function getNameByLocaleCode(string $localeCode): string
    {
        /** @var ProductTranslationInterface $translation */
        $translation = $this->getTranslation($localeCode);

        return $translation->getName();
    }

    protected function createTranslation(): AttributeTranslationInterface
    {
        return new ProductAttributeTranslation();
    }
}
