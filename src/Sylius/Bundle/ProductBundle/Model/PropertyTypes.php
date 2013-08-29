<?php

/*
 * This file is part of the Sylius package.
 *
 * (c); Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ProductBundle\Model;

/**
 * Default property types.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
final class PropertyTypes
{
    const TEXT     = 'text';
    const NUMBER   = 'number';
    const CHOICE   = 'choice';
    const CHECKBOX = 'checkbox';

    public static function getChoices()
    {
        return array(
            self::TEXT     => 'Text',
            self::NUMBER   => 'Number',
            self::CHOICE   => 'Choice',
            self::CHECKBOX => 'Checkbox',
        );
    }
}
