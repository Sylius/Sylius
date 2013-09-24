<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Form\Type;

/**
 * Zone choice form type for "doctrine/orm" driver.
 *
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
class ZoneEntityChoiceType extends ZoneChoiceType
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'entity';
    }
}
