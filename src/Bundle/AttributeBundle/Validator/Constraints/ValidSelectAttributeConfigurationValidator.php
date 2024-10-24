<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AttributeBundle\Validator\Constraints;

use Sylius\Component\Attribute\AttributeType\SelectAttributeType;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class ValidSelectAttributeConfigurationValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        /** @var AttributeInterface $value */
        Assert::isInstanceOf($value, AttributeInterface::class);

        /** @var ValidSelectAttributeConfiguration $constraint */
        Assert::isInstanceOf($constraint, ValidSelectAttributeConfiguration::class);

        if (SelectAttributeType::TYPE !== $value->getType()) {
            return;
        }

        $configuration = $value->getConfiguration();

        $min = null;
        if (!empty($configuration['min'])) {
            $min = $configuration['min'];
        }

        $max = null;
        if (!empty($configuration['max'])) {
            $max = $configuration['max'];
        }

        if (null === $min && null === $max) {
            return;
        }

        $multiple = $value->getConfiguration()['multiple'] ?? false;
        if (!$multiple) {
            $this->context->addViolation($constraint->messageMultiple);

            return;
        }

        if (null !== $min && null !== $max && $min > $max) {
            $this->context->addViolation($constraint->messageMaxEntries);

            return;
        }

        $numberOfChoices = count($value->getConfiguration()['choices']);
        if (null !== $min && $min > $numberOfChoices) {
            $this->context->addViolation($constraint->messageMinEntries);
        }
    }
}
