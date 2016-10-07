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
class CannotDisableLocaleValidator extends ConstraintValidator
{
    /**
     * @var string
     */
    private $baseLocale;

    /**
     * @param string $baseLocale
     */
    public function __construct($baseLocale)
    {
        $this->baseLocale = $baseLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($locale, Constraint $constraint)
    {
        if ($locale->getCode() !== $this->baseLocale) {
            return;
        }
        if ($locale->isEnabled()) {
            return;
        }

        $this->context->addViolation($constraint->message);
    }
}
