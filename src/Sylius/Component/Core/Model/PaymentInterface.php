<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Model;

use Sylius\Component\Order\Model\OrderAwareInterface;
use Sylius\Component\Payment\Model\PaymentInterface as BasePaymentInterface;

/**
 * @author Ka Yue Yeung <kayuey@gmail.com>
 */
interface PaymentInterface extends BasePaymentInterface, OrderAwareInterface
{
}
