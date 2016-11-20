<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CurrencyBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class UniqueCurrencyPair extends Constraint
{
    /**
     * @var string
     */
    public $message = 'sylius.exchange_rate.unique_currency_pair';

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
