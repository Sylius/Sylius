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

use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class AttributeTypeValidator extends ConstraintValidator
{
    public function __construct(private ServiceRegistryInterface $attributeTypeRegistry)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        /** @var mixed|AttributeInterface $value */
        Assert::isInstanceOf($value, AttributeInterface::class);
        /** @var Constraint|AttributeType $constraint */
        Assert::isInstanceOf($constraint, AttributeType::class);

        $type = $value->getType();
        if (null === $type || $this->attributeTypeRegistry->has($type)) {
            return;
        }

        $this->context
            ->buildViolation($constraint->unregisteredAttributeTypeMessage, ['%type%' => $type])
            ->atPath('type')
            ->addViolation()
        ;
    }
}
