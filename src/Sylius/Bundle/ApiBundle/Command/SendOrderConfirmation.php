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

namespace Sylius\Bundle\ApiBundle\Command;

use Sylius\Component\Order\Model\OrderInterface;

class SendOrderConfirmation
{
    /** @var OrderInterface */
    protected $order;

    public function __construct(OrderInterface $order)
    {
        $this->order = $order;
    }

    public function order(): OrderInterface
    {
        return $this->order;
    }
}
