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

final class CatalogPromotionRules extends Constraint
{
    public string $invalidConfiguration = 'sylius.catalog_promotion.rules.invalid_configuration';

    public string $invalidType = 'sylius.catalog_promotion.rules.invalid_type';

    public function validatedBy(): string
    {
        return 'sylius_catalog_promotion_rules';
    }

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
