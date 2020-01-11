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

use Sylius\Component\Attribute\AttributeType\AttributeTypeInterface;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class ValidAttributeValueValidator extends ConstraintValidator
{
    /** @var ServiceRegistryInterface */
    private $attributeTypeRegistry;

    public function __construct(ServiceRegistryInterface $attributeTypeRegistry)
    {
        $this->attributeTypeRegistry = $attributeTypeRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$value instanceof AttributeValueInterface) {
            throw new UnexpectedTypeException(get_class($value), AttributeValueInterface::class);
        }

        /** @var AttributeTypeInterface $attributeType */
        $attributeType = $this->attributeTypeRegistry->get($value->getType());

        $attributeType->validate($value, $this->context, $value->getAttribute()->getConfiguration());
    }
}
