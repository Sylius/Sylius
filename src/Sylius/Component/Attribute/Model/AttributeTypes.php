<?php

/*
 * This file is part of the Sylius package.
 *
 * (c); Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Attribute\Model;

/**
 * Default attribute types.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class AttributeTypes
{
    const CHECKBOX   = 'checkbox';
    const CHOICE     = 'choice';
    const MONEY      = 'money';
    const NUMBER     = 'number';
    const PERCENTAGE = 'percent';
    const TEXT       = 'text';

    public static function getChoices()
    {
        return array(
            self::CHECKBOX   => 'Checkbox',
            self::CHOICE     => 'Choice',
            self::MONEY      => 'Money',
            self::NUMBER     => 'Number',
            self::PERCENTAGE => 'Percentage',
            self::TEXT       => 'Text',
        );
    }
}
