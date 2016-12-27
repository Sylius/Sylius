<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InventoryBundle\Twig;

use Sylius\Bundle\InventoryBundle\Templating\Helper\InventoryHelper;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class InventoryExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
             new \Twig_SimpleFunction('sylius_inventory_is_available', [InventoryHelper::class, 'isStockAvailable']),
             new \Twig_SimpleFunction('sylius_inventory_is_sufficient', [InventoryHelper::class, 'isStockSufficient']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_inventory';
    }
}
