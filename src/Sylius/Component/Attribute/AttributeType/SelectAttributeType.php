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

namespace Sylius\Component\Attribute\AttributeType;

use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class SelectAttributeType implements AttributeTypeInterface
{
    public const TYPE = 'select';

    /**
     * {@inheritdoc}
     */
    public function getStorageType(): string
    {
        return AttributeValueInterface::STORAGE_JSON;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return static::TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(
        AttributeValueInterface $attributeValue,
        ExecutionContextInterface $context,
        array $configuration
    ): void {
        if (!isset($configuration['required']) && !isset($configuration['multiple'])) {
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
     * @param array|null $value
     * @param array $validationConfiguration
     *
     * @return ConstraintViolationListInterface
     */
    private function getValidationErrors(
        ExecutionContextInterface $context,
        ?array $value,
        array $validationConfiguration
    ): ConstraintViolationListInterface {
        $validator = $context->getValidator();

        $constraints = [
            new All([
                new Type([
                    'type' => 'string',
                ]),
            ]),
        ];

        if (isset($validationConfiguration['required'])) {
            $constraints[] = new NotBlank([]);
        }

        if (isset($validationConfiguration['min']) && !empty($validationConfiguration['min'])) {
            $constraints[] = new Count([
                'min' => $validationConfiguration['min'],
            ]);
        }

        if (isset($validationConfiguration['max']) && !empty($validationConfiguration['max'])) {
            $constraints[] = new Count([
                'max' => $validationConfiguration['max'],
            ]);
        }

        return $validator->validate($value, $constraints);
    }
}
