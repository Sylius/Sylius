<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AttributeBundle\AttributeType;

use Sylius\Component\Attribute\AttributeType\AttributeTypeInterface;
use Sylius\Component\Attribute\Model\AttributeValueInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class PercentAttributeType implements AttributeTypeInterface
{
    const TYPE = 'percent';

    /**
     * @return string
     */
    public function getStorageType()
    {
        return AttributeValueInterface::STORAGE_FLOAT;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return static::TYPE;
    }
}
