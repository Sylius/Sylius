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

namespace Sylius\Component\Attribute\Checker;

use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class AttributeDeletionChecker implements AttributeDeletionCheckerInterface
{
    public function __construct(private RepositoryInterface $attributeValueRepository)
    {
    }

    public function isDeletable(ProductAttributeInterface $productAttribute): bool
    {
        $attribute = $this->attributeValueRepository->findOneBy(['attribute' => $productAttribute]);

        return null === $attribute;
    }
}
