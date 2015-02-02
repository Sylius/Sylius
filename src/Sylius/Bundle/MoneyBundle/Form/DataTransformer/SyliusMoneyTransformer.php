<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MoneyBundle\Form\DataTransformer;

use Symfony\Component\Form\Extension\Core\DataTransformer\MoneyToLocalizedStringTransformer;

/**
 * Transformer ensures proper rounding of float value to an integer.
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class SyliusMoneyTransformer extends MoneyToLocalizedStringTransformer
{
    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        $value = parent::reverseTransform($value);

        return intval(round($value));
    }
}