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

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class DefaultAttributeTypes
{
    const TEXT = 'text';

    /**
     * @return array
     */
    public static function getTypes()
    {
        return array(
            self::TEXT => 'sylius.attribute_type.text',
        );
    }
}
