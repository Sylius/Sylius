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

namespace Sylius\Bundle\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/** @experimental */
final class AddingEligibleProductVariantToCart extends Constraint
{
    /** @var string */
    public $productNotExistMessage = 'sylius.product.not_exist';

    /** @var string */
    public $productVariantNotExistMessage = 'sylius.product_variant.not_exist';

    public function validatedBy(): string
    {
        return 'sylius_api_validator_adding_eligible_product_variant_to_cart';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
