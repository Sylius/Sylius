<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Core\Model;

use Sylius\Order\Model\OrderAwareInterface;
use Sylius\Payment\Model\PaymentInterface as BasePaymentInterface;

/**
 * @author Ka Yue Yeung <kayuey@gmail.com>
 */
interface PaymentInterface extends BasePaymentInterface, OrderAwareInterface
{
}
