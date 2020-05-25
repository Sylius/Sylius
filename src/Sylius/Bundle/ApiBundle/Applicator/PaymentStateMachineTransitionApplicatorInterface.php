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

namespace Sylius\Bundle\ApiBundle\Applicator;

use Sylius\Component\Payment\Model\PaymentInterface;

interface PaymentStateMachineTransitionApplicatorInterface
{
    public function complete(PaymentInterface $data): PaymentInterface;
}
