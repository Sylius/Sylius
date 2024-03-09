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

use Sylius\Component\Attribute\AttributeType\AttributeTypeInterface;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class ValidAttributeValueValidator extends ConstraintValidator
{
    public function __construct(private ServiceRegistryInterface $attributeTypeRegistry)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$value instanceof AttributeValueInterface) {
            throw new UnexpectedTypeException($value::class, AttributeValueInterface::class);
        }

        /** @var AttributeTypeInterface $attributeType */
        $attributeType = $this->attributeTypeRegistry->get($value->getType());

        $attributeType->validate($value, $this->context, $value->getAttribute()->getConfiguration());
    }
}
