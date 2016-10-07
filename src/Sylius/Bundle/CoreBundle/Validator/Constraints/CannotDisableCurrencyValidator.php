<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class CannotDisableCurrencyValidator extends ConstraintValidator
{
    /**
     * @var string
     */
    private $baseCurrency;

    /**
     * @param string $baseCurrency
     */
    public function __construct($baseCurrency)
    {
        $this->baseCurrency = $baseCurrency;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($currency, Constraint $constraint)
    {
        if ($currency->getCode() !== $this->baseCurrency) {
            return;
        }
        if ($currency->isEnabled()) {
            return;
        }

        $this->context->addViolation($constraint->message);
    }
}
