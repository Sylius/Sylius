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

namespace Sylius\Bundle\ApiBundle\Command\Payum;

use Sylius\Bundle\ApiBundle\Command\CommandAwareDataTransformerInterface;

/** experimental */
class PayumRequest implements CommandAwareDataTransformerInterface
{
    public function __construct(private string $payum_token)
    {
    }

    public function getPayumToken(): string
    {
        return $this->payum_token;
    }

    public function setPayumToken(string $payum_token): void
    {
        $this->payum_token = $payum_token;
    }
}
