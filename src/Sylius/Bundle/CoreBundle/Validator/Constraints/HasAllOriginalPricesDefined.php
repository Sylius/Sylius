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

final class HasAllOriginalPricesDefined extends Constraint
{
    public string $message = 'sylius.product_variant.channel_pricing.all_original_prices_defined';

    public function validatedBy(): string
    {
        return 'sylius_has_all_original_prices_defined';
    }

    public function getTargets(): string
    {
        return Constraint::CLASS_CONSTRAINT;
    }
}
