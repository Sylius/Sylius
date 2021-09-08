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

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class CatalogPromotionRule extends Constraint
{
    public string $invalidType = 'sylius.catalog_promotion_rule.invalid_type';

    public string $invalidVariants = 'sylius.catalog_promotion_rule.for_variants.invalid_variants';

    public string $notEmpty = 'sylius.catalog_promotion_rule.for_variants.not_empty';

    public function validatedBy(): string
    {
        return 'sylius_catalog_promotion_rule';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
