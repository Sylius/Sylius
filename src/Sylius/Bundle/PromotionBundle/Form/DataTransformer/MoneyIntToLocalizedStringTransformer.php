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

namespace Sylius\Bundle\PromotionBundle\Form\DataTransformer;

use Symfony\Component\Form\Extension\Core\DataTransformer\MoneyToLocalizedStringTransformer;

final class MoneyIntToLocalizedStringTransformer extends MoneyToLocalizedStringTransformer
{
    public function reverseTransform($value)
    {
        if (!is_numeric($value)) {
            return;
        }

        return (int) parent::reverseTransform($value);
    }

    public function transform($value)
    {
        if (!is_numeric($value)) {
            return;
        }

        return parent::transform($value);
    }
}
