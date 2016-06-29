<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\AttributeBundle\Validator\Constraints;

use Sylius\Attribute\Model\AttributeValueInterface;
use Sylius\Registry\ServiceRegistryInterface;
use Sylius\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ValidAttributeValueValidator extends ConstraintValidator
{
    /**
     * @var ServiceRegistryInterface
     */
    private $attributeTypeRegistry;

    /**
     * @param ServiceRegistryInterface $attributeTypeRegistry
     */
    public function __construct(ServiceRegistryInterface $attributeTypeRegistry)
    {
        $this->attributeTypeRegistry = $attributeTypeRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof AttributeValueInterface) {
            throw new UnexpectedTypeException(get_class($value), AttributeValueInterface::class);
        }

        $attributeType = $this->attributeTypeRegistry->get($value->getType());

        $attributeType->validate($value, $this->context, $value->getAttribute()->getConfiguration());
    }
}
