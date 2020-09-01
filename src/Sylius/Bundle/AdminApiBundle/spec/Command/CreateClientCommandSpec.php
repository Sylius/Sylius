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

namespace spec\Sylius\Bundle\AdminApiBundle\Command;

use FOS\OAuthServerBundle\Model\ClientManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\AdminApiBundle\Model\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CreateClientCommandSpec extends ObjectBehavior
{
    public function it_is_a_command(ClientManager $clientManager)
    {
        $this->beConstructedWith($clientManager);
        $this->shouldHaveType(Command::class);
    }

    public function it_has_a_name(ClientManager $clientManager)
    {
        $this->beConstructedWith($clientManager);
        $this->getName()->shouldReturn('sylius:oauth-server:create-client');
    }

    public function it_creates_a_client_without_client_manager(
        InputInterface $input,
        OutputInterface $output,
        ClientManager $clientManager,
        Client $client
    ) {
        $this->beConstructedWith($clientManager);

        $input->bind(Argument::any())->shouldBeCalled();
        $input->isInteractive()->shouldBeCalled();
        $input->validate()->shouldBeCalled();
        $input->hasArgument('command')->willReturn(false);

        $clientManager->createClient()->willReturn($client);

        $input->getOption('redirect-uri')->willReturn(['redirect-uri']);
        $input->getOption('grant-type')->willReturn(['grant-type']);

        $client->setRedirectUris(['redirect-uri'])->shouldBeCalled();
        $client->setAllowedGrantTypes(['grant-type'])->shouldBeCalled();

        $clientManager->updateClient($client)->shouldBeCalled();

        $client->getPublicId()->shouldBeCalled();
        $client->getSecret()->shouldBeCalled();

        $output->writeln(Argument::type('string'))->shouldBeCalled();

        $this->run($input, $output);
    }

    public function it_creates_a_client(
        InputInterface $input,
        OutputInterface $output,
        ClientManager $clientManager,
        Client $client
    ) {
        $this->beConstructedWith($clientManager);

        $input->bind(Argument::any())->shouldBeCalled();
        $input->isInteractive()->shouldBeCalled();
        $input->validate()->shouldBeCalled();
        $input->hasArgument('command')->willReturn(false);

        $clientManager->createClient()->willReturn($client);

        $input->getOption('redirect-uri')->willReturn(['redirect-uri']);
        $input->getOption('grant-type')->willReturn(['grant-type']);

        $client->setRedirectUris(['redirect-uri'])->shouldBeCalled();
        $client->setAllowedGrantTypes(['grant-type'])->shouldBeCalled();

        $clientManager->updateClient($client)->shouldBeCalled();

        $client->getPublicId()->shouldBeCalled();
        $client->getSecret()->shouldBeCalled();

        $output->writeln(Argument::type('string'))->shouldBeCalled();

        $this->run($input, $output);
    }
}
