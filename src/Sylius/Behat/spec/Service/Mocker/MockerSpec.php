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

namespace spec\Sylius\Behat\Service\Mocker;

use Mockery\MockInterface;
use PhpSpec\ObjectBehavior;
use Psr\Http\Client\ClientInterface;
use PSS\SymfonyMockerContainer\DependencyInjection\MockerContainer;
use Sylius\Behat\Service\Mocker\Mocker;
use Sylius\Behat\Service\Mocker\MockerInterface;

final class MockerSpec extends ObjectBehavior
{
    function let(MockerContainer $container)
    {
        $this->beConstructedWith($container);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Mocker::class);
    }

    function it_implements_behat_mocker_interface()
    {
        $this->shouldImplement(MockerInterface::class);
    }

    function it_mocks_given_service($container)
    {
        $container->mock('sylius.payum.http_client', ClientInterface::class)->shouldBeCalled();

        $this->mockService('sylius.payum.http_client', ClientInterface::class);
    }

    function it_mocks_collaborator()
    {
        $this->mockCollaborator(ClientInterface::class)->shouldHaveType(MockInterface::class);
    }
}
