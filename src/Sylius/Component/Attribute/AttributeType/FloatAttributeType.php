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
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class FloatAttributeType implements AttributeTypeInterface
{
    public const TYPE = 'float';

    public function getStorageType(): string
    {
        return AttributeValueInterface::STORAGE_FLOAT;
    }

    public function getType(): string
    {
        return self::TYPE;
    }

    public function validate(
        AttributeValueInterface $attributeValue,
        ExecutionContextInterface $context,
        array $configuration
    ): void {
        if (!isset($configuration['required'])) {
            return;
        }

        $value = $attributeValue->getValue();

        foreach ($this->getValidationErrors($context, $value) as $error) {
            $context
                ->buildViolation($error->getMessage())
                ->atPath('value')
                ->addViolation()
            ;
        }
    }

    private function getValidationErrors(ExecutionContextInterface $context, ?float $value): ConstraintViolationListInterface
    {
        return $context->getValidator()->validate($value, [new NotBlank([])]);
    }
}
