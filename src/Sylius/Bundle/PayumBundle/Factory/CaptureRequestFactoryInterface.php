<?php

declare(strict_types=1);

namespace Sylius\Bundle\PayumBundle\Factory;

use Payum\Core\Security\TokenAggregateInterface;
use Payum\Core\Security\TokenInterface;

interface CaptureRequestFactoryInterface
{
    public function createNewWithToken(TokenInterface $token): TokenAggregateInterface;
}