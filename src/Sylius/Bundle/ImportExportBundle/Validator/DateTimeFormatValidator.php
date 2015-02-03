<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ImportExportBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class DateTimeFormatValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($dateTimeFormat, Constraint $constraint)
    {
        if (false === \DateTime::createFromFormat($dateTimeFormat, 'now')) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%format%', $dateTimeFormat)
                ->addViolation();
        }
    }
}
