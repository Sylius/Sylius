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

namespace Sylius\Bundle\PayumBundle\Factory;

use Payum\Core\Request\Authorize;
use Payum\Core\Security\TokenAggregateInterface;
use Payum\Core\Security\TokenInterface;

final class AuthorizeFactory implements TokenAggregateRequestFactoryInterface
{
    public function createNewWithToken(TokenInterface $token): TokenAggregateInterface
    {
        return new Authorize($token);
    }
}
