<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\PaymentRequest\Checker;

use Sylius\Bundle\CoreBundle\PaymentRequest\Command\PaymentRequestHashAwareInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

interface PaymentRequestIntegrityCheckerInterface
{
    public function check(PaymentRequestHashAwareInterface $command): PaymentRequestInterface;
}
