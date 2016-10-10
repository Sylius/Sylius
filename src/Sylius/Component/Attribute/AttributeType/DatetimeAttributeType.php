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
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class DatetimeAttributeType implements AttributeTypeInterface
{
    const TYPE = 'datetime';

    /**
     * {@inheritdoc}
     */
    public function getStorageType()
    {
        return AttributeValueInterface::STORAGE_DATETIME;
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
    }
}
