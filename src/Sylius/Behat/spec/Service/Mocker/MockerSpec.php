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

namespace spec\Sylius\Behat;

use Mockery\MockInterface;
use Payum\Core\Bridge\Guzzle\HttpClient;
use PhpSpec\ObjectBehavior;
use PSS\SymfonyMockerContainer\DependencyInjection\MockerContainer;
use Sylius\Behat\Service\Mocker\Mocker;
use Sylius\Behat\Service\Mocker\MockerInterface;

final class MockerSpec extends ObjectBehavior
{
    function let(MockerContainer $container): void
    {
        $this->beConstructedWith($container);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(Mocker::class);
    }

    function it_implements_behat_mocker_interface(): void
    {
        $this->shouldImplement(MockerInterface::class);
    }

    function it_mocks_given_service($container): void
    {
        $container->mock('sylius.payum.http_client', HttpClient::class)->shouldBeCalled();

        $this->mockService('sylius.payum.http_client', HttpClient::class);
    }

    function it_mocks_collaborator(): void
    {
        $this->mockCollaborator(HttpClient::class)->shouldHaveType(MockInterface::class);
    }
}
