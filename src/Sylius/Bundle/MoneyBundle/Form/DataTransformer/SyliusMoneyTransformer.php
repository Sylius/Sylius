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

namespace Sylius\Bundle\MoneyBundle\Form\DataTransformer;

use Symfony\Component\Form\Extension\Core\DataTransformer\MoneyToLocalizedStringTransformer;

final class SyliusMoneyTransformer extends MoneyToLocalizedStringTransformer
{
    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value): ?int
    {
        $value = parent::reverseTransform($value);

        return null === $value ? null : (int) round($value);
    }
}
