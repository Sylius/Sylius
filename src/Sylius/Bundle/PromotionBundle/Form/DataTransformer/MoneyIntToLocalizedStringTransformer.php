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

use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\DataTransformer\MoneyToLocalizedStringTransformer;

final class MoneyIntToLocalizedStringTransformer extends MoneyToLocalizedStringTransformer
{
    /**
     * Transforms a localized money string into a normalized format.
     *
     * @param string $value Localized money string
     *
     * @return int|float|null
     *
     * @throws TransformationFailedException if the given value is not a string
     *                                       or if the value cannot be transformed
     */
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
