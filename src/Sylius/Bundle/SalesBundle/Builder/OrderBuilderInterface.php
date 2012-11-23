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

use Sylius\Bundle\SalesBundle\Model\OrderInterface;

/**
 * Order builder interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface OrderBuilderInterface
{
    function build(OrderInterface $order);
    function finalize(OrderInterface $order);
}
