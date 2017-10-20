<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\InventoryBundle\Twig;

use Sylius\Bundle\InventoryBundle\Templating\Helper\InventoryHelper;

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
    public function getFunctions(): array
    {
        return [
             new \Twig_Function('sylius_inventory_is_available', [$this->helper, 'isStockAvailable']),
             new \Twig_Function('sylius_inventory_is_sufficient', [$this->helper, 'isStockSufficient']),
        ];
    }
}
