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

use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Bundle\SalesBundle\Model\OrderInterface;

/**
 * Order builder.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderBuilder implements OrderBuilderInterface
{
    protected $itemRepository;

    public function __construct(ObjectRepository $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }

    public function build(OrderInterface $order)
    {
        $order->calculateTotal();
    }

    public function finalize(OrderInterface $order)
    {
    }

    protected function createNewItem()
    {
        return $this->itemRepository->createNew();
    }
}
