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

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Product\Model\ProductAttributeValue;

final class AttributeDeletionChecker implements AttributeDeletionCheckerInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function isDeletable(ProductAttributeInterface $productAttribute): bool
    {
        $attribute = $this->entityManager->getRepository(ProductAttributeValue::class)->findOneBy(['attribute' => $productAttribute]);

        return null === $attribute;
    }
}
