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

namespace Sylius\Bundle\CurrencyBundle\Validator\Constraints;

use Sylius\Component\Currency\Model\ExchangeRateInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Webmozart\Assert\Assert;

class DifferentSourceTargetCurrencyValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        /** @var DifferentSourceTargetCurrency $constraint */
        Assert::isInstanceOf($constraint, DifferentSourceTargetCurrency::class);

        if (!$value instanceof ExchangeRateInterface) {
            throw new UnexpectedTypeException($value, ExchangeRateInterface::class);
        }

        if ($value->getSourceCurrency() === $value->getTargetCurrency()) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
