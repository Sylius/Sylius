<?php

namespace spec\Sylius\Bundle\PayumBundle\Factory;

use Payum\Core\Security\TokenInterface;
use Sylius\Bundle\PayumBundle\Factory\GetStatusFactory;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PayumBundle\Factory\GetStatusFactoryInterface;
use Sylius\Bundle\PayumBundle\Request\GetStatus;

final class GetStatusFactorySpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(GetStatusFactory::class);
    }

    function it_is_get_status_factory(): void
    {
        $this->shouldImplement(GetStatusFactoryInterface::class);
    }

    function it_creates_get_status_request(TokenInterface $token): void
    {
        $this->createNewWithModel($token)->shouldBeLike(new GetStatus($token->getWrappedObject()));
    }
}
