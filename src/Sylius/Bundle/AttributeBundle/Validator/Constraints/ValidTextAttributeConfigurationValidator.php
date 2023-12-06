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

use Sylius\Component\Attribute\AttributeType\TextAttributeType;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class ValidTextAttributeConfigurationValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        /** @var AttributeInterface $value */
        Assert::isInstanceOf($value, AttributeInterface::class);

        /** @var ValidTextAttributeConfiguration $constraint */
        Assert::isInstanceOf($constraint, ValidTextAttributeConfiguration::class);

        if (TextAttributeType::TYPE !== $value->getType()) {
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

        if (null !== $min && null !== $max && $min > $max) {
            $this->context->addViolation($constraint->message);
        }
    }
}
