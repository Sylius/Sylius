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

use Sylius\Component\Currency\Model\ExchangeRateInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class DifferentBaseCounterCurrencyValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        Assert::isInstanceOf($value, ExchangeRateInterface::class);

        if ($value->getBaseCurrency() === $value->getCounterCurrency()) {
            $this->context->addViolation($constraint->message);
        }
    }
}
