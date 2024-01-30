<?php

declare(strict_types=1);

namespace Sylius\Bundle\PayumBundle\Factory;

use Payum\Core\Request\Capture;
use Payum\Core\Security\TokenAggregateInterface;
use Payum\Core\Security\TokenInterface;

final class CaptureFactory implements CaptureRequestFactoryInterface
{
    public function createNewWithToken(TokenInterface $token): TokenAggregateInterface
    {
        return new Capture($token);
    }
}
