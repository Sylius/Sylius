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

namespace Sylius\Bundle\PromotionBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

trigger_deprecation(
    'sylius/promotion-bundle',
    '1.14',
    'The "%s" class is deprecated and will be removed in Sylius 2.0, use the usual symfony logic for validation.',
    CatalogPromotionScope::class,
);
final class CatalogPromotionScope extends Constraint
{
    public string $invalidType = 'sylius.catalog_promotion_scope.invalid_type';

    public function validatedBy(): string
    {
        return 'sylius_catalog_promotion_scope';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
