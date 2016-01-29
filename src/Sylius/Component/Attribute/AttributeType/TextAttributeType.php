<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Attribute\AttributeType;

use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class TextAttributeType implements AttributeTypeInterface
{
    const TYPE = 'text';

    /**
     * {@inheritdoc}
     */
    public function getStorageType()
    {
        return AttributeValueInterface::STORAGE_TEXT;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return static::TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(AttributeValueInterface $attributeValue, ExecutionContextInterface $context, array $configuration)
    {
        if (!isset($configuration['min']) || !isset($configuration['max'])) {
            return;
        }

        $value = $attributeValue->getValue();

        foreach ($this->getValidationErrors($context, $value, $configuration) as $error) {
            $context
                ->buildViolation($error->getMessage())
                ->atPath('value')
                ->addViolation()
            ;
        }
    }

    /**
     * @param ExecutionContextInterface $context
     * @param string $value
     * @param array $validationConfiguration
     *
     * @return ConstraintViolationListInterface
     */
    private function getValidationErrors(ExecutionContextInterface $context, $value, array $validationConfiguration)
    {
        $validator = $context->getValidator();

        return $validator->validate(
            $value,
            new Length([
                'min' => $validationConfiguration['min'],
                'max' => $validationConfiguration['max'],
            ])
        );
    }
}
