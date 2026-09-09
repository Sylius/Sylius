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
use Sylius\Component\Attribute\AttributeType\DatetimeAttributeType;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class ValidDateAttributeConfigurationValidator extends ConstraintValidator
{
    private const SUPPORTED_TYPES = [DateAttributeType::TYPE, DatetimeAttributeType::TYPE];

    public function validate(mixed $value, Constraint $constraint): void
    {
        /** @var AttributeInterface $value */
        Assert::isInstanceOf($value, AttributeInterface::class);

        /** @var ValidDateAttributeConfiguration $constraint */
        Assert::isInstanceOf($constraint, ValidDateAttributeConfiguration::class);

        if (!in_array($value->getType(), self::SUPPORTED_TYPES, true)) {
            return;
        }

        $format = $value->getConfiguration()['format'] ?? null;

        if (null === $format || '' === $format) {
            return;
        }

        if (in_array($format, ValidDateAttributeConfiguration::AVAILABLE_FORMATS, true)) {
            return;
        }

        $this->context
            ->buildViolation($constraint->message)
            ->setParameter('{{ available_formats }}', implode('", "', ValidDateAttributeConfiguration::AVAILABLE_FORMATS))
            ->atPath('configuration[format]')
            ->addViolation()
        ;
    }
}
