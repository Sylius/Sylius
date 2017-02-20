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
     * @var InventoryHelper
     */
    private $helper;

    /**
     * @param InventoryHelper $helper
     */
    public function __construct(InventoryHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
             new \Twig_SimpleFunction('sylius_inventory_is_available', [$this->helper, 'isStockAvailable']),
             new \Twig_SimpleFunction('sylius_inventory_is_sufficient', [$this->helper, 'isStockSufficient']),
        ];
    }
}
