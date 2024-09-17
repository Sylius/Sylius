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
    CatalogPromotionAction::class,
);
final class CatalogPromotionAction extends Constraint
{
    public string $invalidType = 'sylius.catalog_promotion_action.invalid_type';

    public string $invalidDiscount = 'sylius.catalog_promotion_action.percentage_discount.not_valid';

    public string $notInRangeDiscount = 'sylius.catalog_promotion_action.percentage_discount.not_in_range';

    public string $notNumberOrEmpty = 'sylius.catalog_promotion_action.percentage_discount.not_number_or_empty';

    public function validatedBy(): string
    {
        return 'sylius_catalog_promotion_action';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
