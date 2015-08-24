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
        $date = new \DateTime();
        $formattedDate = $date->format($dateTimeFormat);

        if ($date != \DateTime::createFromFormat($dateTimeFormat, $formattedDate)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%format%', $dateTimeFormat)
                ->addViolation();
        }
    }
}
