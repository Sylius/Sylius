<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Event;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;
use Sylius\Component\Core\Model\CustomerInterface;

class CustomerShowMenuBuilderEvent extends MenuBuilderEvent
{
    public function __construct(FactoryInterface $factory, ItemInterface $menu, private CustomerInterface $customer)
    {
        parent::__construct($factory, $menu);
    }

    public function getCustomer(): CustomerInterface
    {
        return $this->customer;
    }
}
