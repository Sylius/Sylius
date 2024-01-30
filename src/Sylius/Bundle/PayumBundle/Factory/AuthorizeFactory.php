<?php

declare(strict_types=1);

namespace Sylius\Bundle\PayumBundle\Factory;

use Payum\Core\Request\Authorize;
use Payum\Core\Security\TokenAggregateInterface;
use Payum\Core\Security\TokenInterface;

final class AuthorizeFactory implements AuthorizeRequestFactoryInterface
{
    public function createNewWithToken(TokenInterface $token): TokenAggregateInterface
    {
        return new Authorize($token);
    }
}
