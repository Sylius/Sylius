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

namespace Sylius\Bundle\AttributeBundle\Validator\Constraints;

use Sylius\Component\Attribute\Model\AttributeTranslationInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class ValidAttributeNameValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $notTranslatableName = $value->getNotTranslatableName();
        $translatable = $value->isTranslatable();

        /** @var AttributeTranslationInterface $translation */
        $translation = $value->getTranslation();
        /** @var string|null $translationName */
        $translationName = $translation->getName();

        if ($translatable && $translationName === null) {
            $this->context->addViolation($constraint->messageAttributeNameNotBlank);
        }

        if (!$translatable && $notTranslatableName === null) {
            $this->context->addViolation($constraint->messageAttributeNotTranslatableNameNotBlank);
        }
    }
}
