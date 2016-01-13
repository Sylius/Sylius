<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Packaging;

use Sylius\Component\Inventory\Model\InventorySubjectInterface;

/**
 * Items builder.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ItemsFactory implements ItemsFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createItems(InventorySubjectInterface $subject)
    {
        return new Items($subject);
    }
}
