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

namespace Sylius\Component\Attribute\AttributeType;

use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class TextAttributeType implements AttributeTypeInterface
{
    public const TYPE = 'text';

    public function getStorageType(): string
    {
        return AttributeValueInterface::STORAGE_TEXT;
    }

    public function getType(): string
    {
        return self::TYPE;
    }

    public function validate(
        AttributeValueInterface $attributeValue,
        ExecutionContextInterface $context,
        array $configuration,
    ): void {
        if (!isset($configuration['required']) && (!isset($configuration['min']) || !isset($configuration['max']))) {
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

    private function getValidationErrors(
        ExecutionContextInterface $context,
        ?string $value,
        array $validationConfiguration,
    ): ConstraintViolationListInterface {
        $validator = $context->getValidator();
        $constraints = [];

        if (isset($validationConfiguration['required'])) {
            $constraints = [new NotBlank([])];
        }

        if (isset($validationConfiguration['min'], $validationConfiguration['max'])) {
            $constraints[] = new Length(
                [
                    'min' => $validationConfiguration['min'],
                    'max' => $validationConfiguration['max'],
                ],
            );
        }

        return $validator->validate(
            $value,
            $constraints,
        );
    }
}
