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

use Sylius\Component\Attribute\AttributeType\DateAttributeType;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class ValidDateAttributeConfigurationValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        /** @var AttributeInterface $value */
        Assert::isInstanceOf($value, AttributeInterface::class);

        /** @var ValidDateAttributeConfiguration $constraint */
        Assert::isInstanceOf($constraint, ValidDateAttributeConfiguration::class);

        if (DateAttributeType::TYPE !== $value->getType()) {
            return;
        }

        $configuration = $value->getConfiguration();

        if (empty($configuration['format'])) {
            $this->context->buildViolation($constraint->notBlank)->atPath('configuration[format]')->addViolation();

            return;
        }

        $date = \DateTime::createFromFormat('Y-m-d H:i:s', '2024-01-01');

        if (false === $date) {
            $this->context->buildViolation($constraint->invalidFormat)->atPath('configuration[format]')->addViolation();
        }
    }
}
