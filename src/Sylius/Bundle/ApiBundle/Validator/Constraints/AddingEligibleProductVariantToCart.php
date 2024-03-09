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

namespace Sylius\Bundle\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class AddingEligibleProductVariantToCart extends Constraint
{
    public string $productNotExistMessage = 'sylius.product.not_exist';

    public string $productVariantNotExistMessage = 'sylius.product_variant.not_exist';

    public string $productVariantNotSufficient = 'sylius.product_variant.not_sufficient';

    public function validatedBy(): string
    {
        return 'sylius_api_validator_adding_eligible_product_variant_to_cart';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
