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

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class CatalogPromotionScope extends Constraint
{
    public string $invalidType = 'sylius.catalog_promotion_scope.invalid_type';

    public string $invalidVariants = 'sylius.catalog_promotion_scope.for_variants.invalid_variants';

    public string $variantsNotEmpty = 'sylius.catalog_promotion_scope.for_variants.not_empty';

    public string $invalidTaxons = 'sylius.catalog_promotion_scope.for_taxons.invalid_taxons';

    public string $taxonsNotEmpty = 'sylius.catalog_promotion_scope.for_taxons.not_empty';

    public string $invalidProducts = 'sylius.catalog_promotion_scope.for_products.invalid_products';

    public string $productsNotEmpty = 'sylius.catalog_promotion_scope.for_products.not_empty';

    public function validatedBy(): string
    {
        return 'sylius_catalog_promotion_scope';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
