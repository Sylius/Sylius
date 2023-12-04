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

namespace Sylius\Component\Product\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\Component\Attribute\Repository\AttributeValueRepositoryInterface as BaseAttributeValueRepositoryInterface;

/**
 * @template T of ProductAttributeValueInterface
 *
 * @extends BaseAttributeValueRepositoryInterface<T>
 */
interface ProductAttributeValueRepositoryInterface extends BaseAttributeValueRepositoryInterface
{
    /**
     * @return array|ProductAttributeValueInterface[]
     */
    public function findByJsonChoiceKey(string $choiceKey): array;

    public function createByProductCodeAndLocaleQueryBuilder(
        string $productCode,
        string $localeCode,
        ?string $fallbackLocaleCode,
        ?string $defaultLocaleCode,
    ): QueryBuilder;
}
