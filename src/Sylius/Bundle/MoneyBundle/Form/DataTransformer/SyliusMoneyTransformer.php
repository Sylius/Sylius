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

namespace Sylius\Bundle\MoneyBundle\Form\DataTransformer;

use Symfony\Component\Form\Extension\Core\DataTransformer\MoneyToLocalizedStringTransformer;

final class SyliusMoneyTransformer extends MoneyToLocalizedStringTransformer
{
    public function reverseTransform($value): ?int
    {
        /** @var int|float|null $value */
        $value = parent::reverseTransform($value);

        return null === $value ? null : (int) round($value);
    }
}
