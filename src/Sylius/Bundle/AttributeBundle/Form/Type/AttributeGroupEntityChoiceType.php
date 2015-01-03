<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AttributeBundle\Form\Type;

/**
 * Attribute choice form type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class AttributeGroupEntityChoiceType extends AttributeGroupChoiceType
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'entity';
    }
}
