<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Builder;

use Sylius\Bundle\ResourceBundle\Manager\ResourceManagerInterface;
use Sylius\Bundle\SalesBundle\Model\OrderInterface;

/**
 * Order builder.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
abstract class OrderBuilder implements OrderBuilderInterface
{
    protected $itemManager;

    public function __construct(ResourceManagerInterface $itemManager)
    {
        $this->itemManager = $itemManager;
    }

    public function finalize(OrderInterface $order)
    {
    }
}
