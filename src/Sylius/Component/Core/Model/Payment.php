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

namespace Sylius\Component\Core\Model;

use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Payment\Model\Payment as BasePayment;
use Webmozart\Assert\Assert;

class Payment extends BasePayment implements PaymentInterface
{
    /** @var BaseOrderInterface */
    protected $order;

    public function getOrder(): ?BaseOrderInterface
    {
        Assert::isInstanceOf($this->order, OrderInterface::class);

        return $this->order;
    }

    public function setOrder(?BaseOrderInterface $order): void
    {
        $this->order = $order;
    }
}
