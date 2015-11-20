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
    const CHECKBOX = 'checkbox';
    const DATETIME = 'datetime';
    const INTEGER  = 'integer';
    const PERCENT  = 'percent';
    const TEXT     = 'text';
    const TEXTAREA = 'textarea';

    const STORAGE_TEXT     = 'text';
    const STORAGE_BOOLEAN  = 'boolean';
    const STORAGE_DATETIME = 'datetime';
    const STORAGE_INTEGER  = 'integer';
    const STORAGE_FLOAT    = 'float';

    /**
     * @return array
     */
    public static function getTypes()
    {
        return array(
            self::CHECKBOX => 'sylius.attribute_type.checkbox',
            self::DATETIME => 'sylius.attribute_type.datetime',
            self::INTEGER  => 'sylius.attribute_type.integer',
            self::PERCENT  => 'sylius.attribute_type.percent',
            self::TEXT     => 'sylius.attribute_type.text',
            self::TEXTAREA => 'sylius.attribute_type.textarea',
        );
    }
}
