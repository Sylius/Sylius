<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InventoryBundle\Form\Type;

/**
 * StockLocation entity choice form.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StockLocationEntityType extends StockLocationChoiceType
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'entity';
    }
}
