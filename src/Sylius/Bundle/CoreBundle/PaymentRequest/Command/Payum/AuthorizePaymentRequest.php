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

namespace Sylius\Bundle\CoreBundle\PaymentRequest\Command\Payum;

use Sylius\Bundle\CoreBundle\PaymentRequest\Command\PaymentRequestHashAwareInterface;
use Sylius\Bundle\CoreBundle\PaymentRequest\Command\PaymentRequestHashAwareTrait;

class AuthorizePaymentRequest implements PaymentRequestHashAwareInterface
{
    use PaymentRequestHashAwareTrait;

    public function __construct(
        protected ?string $hash,
    ) {
    }
}
