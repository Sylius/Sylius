<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AttributeBundle\Validator\Constraints;

use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class AttributeValueValidator extends ConstraintValidator
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
            return;
        }

        $attributeType = $this->attributeTypeRegistry->get($value->getType());

        $attributeType->validate($value, $this->context);
    }
}
