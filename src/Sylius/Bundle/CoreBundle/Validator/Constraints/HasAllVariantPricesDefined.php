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

final class HasAllVariantPricesDefined extends Constraint
{
    /**
     * @var string
     */
    public $message = 'sylius.product.variants.all_prices_defined';

    /**
     * {@inheritdoc}
     */
    public function validatedBy(): string
    {
        return 'sylius_has_all_variant_prices_defined';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets(): string
    {
        return Constraint::CLASS_CONSTRAINT;
    }
}
