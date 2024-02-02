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

namespace Sylius\Bundle\AttributeBundle\Tests\Stub;

use Sylius\Bundle\AttributeBundle\Attribute\AsAttributeType;
use Sylius\Component\Attribute\AttributeType\AttributeTypeInterface;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[AsAttributeType(type: 'test', label: 'Test', formType: 'SomeFormType', priority: 15)]
final class AttributeTypeStub implements AttributeTypeInterface
{
    public function getStorageType(): string
    {
        return '';
    }

    public function getType(): string
    {
        return '';
    }

    public function validate(AttributeValueInterface $attributeValue, ExecutionContextInterface $context, array $configuration): void
    {
    }
}
